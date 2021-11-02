<?php

namespace App\Services\Api;

use App\Contracts\Repositories\CommentHistoryRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Repositories\CommentRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Services\Api\RequestServiceInterface;
use App\Services\AbstractService;
use App\Exceptions\QueryException;
use App\Mail\RequestEmail;
use App\Mail\DeleteRequestEmail;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class RequestService extends AbstractService implements RequestServiceInterface
{
    /**
     * @var RequestRepositoryInterface
     */
    protected $requestRepository;
    protected $commentRepository;
    protected $historyRepository;
    protected $userRepository;
    protected $categoryRepository;

    /**
     * UserService constructor.
     * @param RequestRepositoryInterface $requestRepository
     */
    public function __construct(
        RequestRepositoryInterface $requestRepository,
        CommentRepositoryInterface $commentRepository,
        CommentHistoryRepositoryInterface $historyRepository,
        UserRepositoryInterface $userRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->requestRepository = $requestRepository;
        $this->commentRepository = $commentRepository;
        $this->historyRepository = $historyRepository;
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param $params
     * @return array
     */
    public function index($per_page, $params, $page)
    {
        $getCache = config('constants.STATUS_CACHE.GET_CACHE');
        $queryDB = config('constants.STATUS_CACHE.QUERY_DB');
        $queryNoCache = config('constants.STATUS_CACHE.QUERY_NO_CACHE');
        if (!Redis::exists('request:' . $page)) {
            $temp = $queryDB;
        }
        if (Redis::exists('request:' . $page)) {
            $temp = $getCache;
        }
        if ($this->checkParams($params) || $per_page != 10) {
            $temp = $queryNoCache;
        }
        switch ($temp) {
            case $queryDB:
                $request = $this->requestRepository->filter($params, $per_page);
                $this->setCache('request:' . $page, $request);
                return $this->getCache('request:' . $page);
                break;
            case $getCache:
                return $this->getCache('request:' . $page);
                break;
            case $queryNoCache:
                return $this->requestRepository->filter($params, $per_page);
        }
    }

    public function checkParams($params)
    {
        if ($params['name_request'] != '' ||
            $params['content_request'] != '' ||
            $params['due_date_request'] != '' ||
            $params['author_request'] != '' ||
            $params['assign_request'] != '' ||
            $params['category_request'] != '' ||
            $params['department_id'] != '' ||
            $params['status'][0] != '') {
            return true;
        }
        return false;
    }

    public function setCache($name, $request)
    {
        Redis::setex($name, 300, json_encode($request));
    }

    public function getCache($name)
    {
        return json_decode(Redis::get($name));
    }
    
    public function show($id)
    {
        $with = [
            'user:id,name,department_id',
            'manager:id,name',
            'category:id,name'
        ];
        try {
            $request = $this->requestRepository->getColumns(['*'], $with)
                ->where('id', $id)->first();
        } catch (\Throwable $th) {
            return [
                'code' => 500,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ];
        }
        return [
            'code' => 200,
            'request' => $this->requestRepository->getColumns(['*'], $with)
                ->where('id', $id)->get(),
            'comments' => $this->commentRepository
                ->getColumns(['*'], ['user:id,name'])
                ->where('request_id', $id)->orderBy('id', 'DESC')->get(),
            'manager_user' => $this->userRepository->findManagerUser($request->user->department_id),
            'message' => ' success !'
        ];
    }

    public function showMyRequest($userId)
    {
        return [
            'code' => 200,
            'data' => $this->requestRepository->getMyRequest($userId),
            'message' => 'Success'
        ];
    }

    public function create($params)
    {
        Redis::flushDB();
        $request = $this->requestRepository->store($params);
        $user_manager = $this->userRepository->find($request->manager_id);
        $user_author = $this->userRepository->find($request->user_id);
        $contents = [
            'Name-Request' => $request->name,
            'Category' => $request->category->name,
            'Due-Date' => $request->due_date,
            'Assign' => $user_manager->name,
            'Author' => $user_author->name,
            'Content' => $request->content,
            'Priority' => Config::get('constants.GET_PRIORITY_REQUEST.' . $request->priority),
            'Created_at' => date("d-m-Y", strtotime($request->created_at)),
            'Status' => Config::get('constants.GET_STATUS_REQUEST.' . $request->status),
        ];
        $dataComment = [
            'content' => 'Created request ' . $request->name,
            'user_id' => $user_author->id,
            'request_id' => $request->id,
            'title' => 'created'
        ];
        $this->commentRepository->store($dataComment);
        $email = [$user_manager->email, $user_author->email];
        $data = [
            'title' => 'Create-Request: ' . $request->name . ' / ' . date("d-m-Y", strtotime($request->created_at)),
            'category' => $this->categoryRepository->find($request->category_id)->name
        ];
        if (!empty($email)) {
            Mail::to($email)->send(new RequestEmail($contents, $data));
        }
        return [
            'code' => 200,
            'message' => 'Thêm request thành công !',
            'request' => $request
        ];
    }

    public function update($params, $id)
    {
        Redis::flushDB();
        $request_old = $this->requestRepository->find($id);
        $user = JWTAuth::parseToken()->authenticate();
        if (Gate::forUser($user)->allows('update-request', $request_old)) {
            $params['due_date'] = date("Y-m-d 00:00:00", strtotime($params['due_date']));
            return $this->requestDiff($params, $id, $request_old, $user);
        }
        if (Gate::forUser($user)->allows('update-request-admin', $request_old)) {
            $params = array_diff_key(
                $params,
                [
                    'due_date' => $params['due_date'],
                    'category_id' => $params['category_id'],
                    'content' => $params['content'],
                    'name' => $params['name'],
                ]
            );
            return $this->requestDiff($params, $id, $request_old, $user);
        }
        return [
            'code' => 403,
            'message' => 'Bạn không có quyền !'
        ];
    }

    public function delete($id)
    {
        Redis::flushDB();
        $user = JWTAuth::parseToken()->authenticate();
        $request = $this->requestRepository->find($id);
        $statusOpen = Config::get('constants.status.open');
        $statusRequest = $this->requestRepository->getStatusRequest($request);
        if ($request->user_id == $user->id && $statusRequest == $statusOpen) {
            $idRequest = $this->requestRepository->getIdRequest($request);
            $arr = [$idRequest];
            $idComments = $this->commentRepository->getIdsComment($arr);
            $this->commentRepository->destroyMulti($idComments);
            $this->requestRepository->destroy($request);
            $user_manager = $this->userRepository->find($request->manager_id);
            if (!empty($user_manager)) {
                $email[] = $user_manager->email;
            }
            $email[] = $user->email;
            $data = [
                'request' => $request->name,
                'user' => $user->name,
                'deleted_at' => Carbon::now()
            ];
            $dataComment = [
                'content' => 'Delete request ' . $request->name,
                'user_id' => $user->id,
                'request_id' => $request->id,
                'title' => 'deleted'
            ];
            $this->commentRepository->store($dataComment);
            Mail::to($email)->send(new DeleteRequestEmail($data));
            return [
                'message' => 'Xóa thành công !',
            ];
        }
        return [
            'message' => 'Bạn không có quyền xóa !'
        ];
    }

    public function changeStatus($idRequest, $status)
    {
        Redis::flushDB();
        $user = JWTAuth::parseToken()->authenticate();
        $request = $this->requestRepository->find($idRequest);
        $request_author_email = $this->userRepository->find($request->user_id)->email;
        $request_manager_email = $this->userRepository->find($request->manager_id)->email;
        $data['status'] = $status;
        $data['idRequest'] = $idRequest;
        $data['request_author_email'] = $request_author_email;
        $statusRequestOld = $request->status;
        $statusOpen = config('constants.status.open');
        $statusInProgress = config('constants.status.inProgress');
        $statusClose = config('constants.status.close');
        $statusApprove = config('constants.status.approve');
        if (Gate::forUser($user)->allows('change-status-admin', $request)) {
            if (($statusRequestOld == $statusOpen || $statusRequestOld == $statusApprove)
                && ($status == $statusInProgress || $status == $statusClose)
            ) {
                return $this->getMessageChangeStatusRequest($request, $user, $data);
            }
            if ($statusRequestOld == $statusInProgress && $status == $statusClose) {
                return $this->getMessageChangeStatusRequest($request, $user, $data);
            }
            return [
                'code' => 403,
                'message' => 'Trạng thái không được phép thay đổi !',
            ];
        }
        if (Gate::forUser($user)->allows('change-status-manager', $request)) {
            $data['request_manager_email'] = $request_manager_email;
            if ($statusRequestOld == $statusOpen && ($status == $statusApprove || $status == $statusClose)) {
                return $this->getMessageChangeStatusRequest($request, $user, $data);
            }
            return [
                'code' => 403,
                'message' => 'Trạng thái không được phép thay đổi !',
            ];
        }
        return [
            'code' => 403,
            'message' => 'Bạn không có quyền !'
        ];
    }

    public function getMessageChangeStatusRequest($request, $user, $data)
    {
        return [
            'code' => 200,
            'message' => 'Thay đổi status thành công !',
            'request' => $this->updateStatusRequestAndSendEmail($request, $user, $data)
        ];
    }

    public function updateStatusRequestAndSendEmail($request, $user, $data)
    {
        Redis::flushDB();
        $this->requestRepository->changeStatus($data['idRequest'], $data['status']);
        $contents = [
            'status' => config('constants.GET_STATUS_REQUEST.' . $request->status)
                . '->' . config('constants.GET_STATUS_REQUEST.' . $data['status'])
        ];
        $emails = [
            $user->email,
            $data['request_author_email']
        ];
        if (!empty($data['request_manager_email'])) {
            $emails[] = $data['request_manager_email'];
        }
        $data = [
            'title' => 'Update-status: ' . $request->name . ' / '
                . date("d-m-Y", strtotime(Carbon::now()->toDateString())),
            'category' => $this->categoryRepository->find($request->category_id)->name
        ];
        $dataComment = [
            'content' => json_encode($contents),
            'user_id' => $user->id,
            'request_id' => $request->id,
            'title' => 'updated'
        ];
        $this->commentRepository->store($dataComment);
        Mail::to($emails)->send(new RequestEmail($contents, $data));
        return $contents;
    }

    public function requestDiff($params, $id, $request_old, $user)
    {
        Redis::flushDB();
        $request_new = $this->requestRepository->update($params, $id, $request_old);
        $contents = [];
        $request_new->wasChanged('name') ? $contents['Name'] = $request_old->name
            . ' -> ' . $request_new->name : '';
        $request_new->wasChanged('category_id') ? $contents['Category'] =
            $this->categoryRepository->find($request_old->category_id)->name
            . ' -> ' . $this->categoryRepository->find($request_new->category_id)->name : '';
        $request_new->wasChanged('due_date') ?
            $contents['DueDate'] = date("d-m-Y", strtotime($request_old->due_date))
            . ' -> ' . date("d-m-Y", strtotime($request_new->due_date)) : '';
        $request_new->wasChanged('manager_id') ? $contents['Assige'] =
            $this->userRepository->find($request_old->manager_id)->name
            . ' -> ' . $this->userRepository->find($request_new->manager_id)->name : '';
        $request_new->wasChanged('priority') ? $contents['Priority'] =
            Config::get('constants.GET_PRIORITY_REQUEST.' . $request_old->priority)
            . ' -> ' .
            Config::get('constants.GET_PRIORITY_REQUEST.' . $request_new->priority) : '';
        $request_new->wasChanged('content') ? $contents['Content'] = $request_old->content
            . ' -> ' . $request_new->content : '';
        if (!empty($contents)) {
            $email = [];
            $dataComment = [
                'content' => json_encode($contents),
                'user_id' => $user->id,
                'request_id' => $request_new->id,
                'title' => 'updated'
            ];
            $this->commentRepository->store($dataComment);
            $email[] = $user->email;
            $request_new->wasChanged('manager_id') ? $email[] =
                $this->userRepository->find($request_old->manager_id)->email : '';
            $email[] = $this->userRepository->find($request_new->manager_id)->email;
            $data = [
                'title' => $request_new->name . ' / ' . date("d-m-Y", strtotime($request_new->updated_at)),
                'category' => $this->categoryRepository->find($request_new->category_id)->name
            ];
            if (!empty($email)) {
                Mail::to($email)->send(new RequestEmail($contents, $data));
            }
            return [
                'code' => 200,
                'message' => 'Thay đổi thành công !',
                'request' => $contents
            ];
        } else {
            return [
                'code' => 201,
                'message' => 'Không có gì thay đổi !'
            ];
        }
    }

    public function getRequestDueDate()
    {
        return $this->requestRepository->getRequestDueDate()->get();
    }
}
