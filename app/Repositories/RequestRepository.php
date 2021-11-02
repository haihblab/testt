<?php

namespace App\Repositories;

use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Models\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use PhpParser\Node\Stmt\Return_;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class RequestRepository extends BaseRepository implements RequestRepositoryInterface
{
    /**
     * UserRepository constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function paginates($litmit)
    {
        return $this->model->with('User', 'Manager')->orderby('id', 'DESC')->paginate($litmit);
    }

    public function filter($params, $per_page)
    {
        if (empty($params['status'][0])) {
            $statusOpenRequest = config('constants.status.open');
            $statusInProgressRequest = config('constants.status.inProgress');
            $statusApproveRequest = config('constants.status.approve');
            $params['status'] =  [
                $statusOpenRequest,
                $statusInProgressRequest,
                $statusApproveRequest
            ];
        }
        $with = [
            'manager:id,name',
            'user:id,name,role_id,department_id',
            'category:id,name',
            'user.department:id,name'
        ];
        return $this->model->select('requests.*')->with($with)
            ->join('users', 'requests.user_id', '=', 'users.id')
            ->when($params['name_request'], function ($query, $name_request) {
                return $query->where('requests.name', 'like', "%$name_request%");
            })->when($params['content_request'], function ($query, $content_request) {
                return $query->where('content', 'like', "%$content_request%");
            })->when($params['status'], function ($query, $status) {
                return $query->whereIn('requests.status', $status);
            })->when($params['due_date_request'], function ($query, $due_date_request) {
                return $query->where('due_date', 'like', "%$due_date_request%");
            })->when($params['author_request'], function ($query, $author_request) {
                return $query->where('user_id', $author_request);
            })->when($params['assign_request'], function ($query, $assign_request) {
                return $query->where('manager_id', $assign_request);
            })->when($params['category_request'], function ($query, $category_request) {
                return $query->where('category_id', $category_request);
            })->when($params['department_id'], function ($query, $department) {
                return $query->where('department_id', $department);
            })->orderBy('id', 'DESC')->paginate($per_page);
    }

    public function listNameRequest($id)
    {
        return Rule::unique('requests', 'id')->ignore($id);
    }

    public function update($params, $id)
    {
        $request_old = $this->model->findOrFail($id);
        return $request_old->updateOrCreate(['id' => $id], $params);
    }

    public function getIdsRequest($arr)
    {
        return $this->model->WhereIn('category_id', $arr)->pluck('id')->all();
    }

    public function getStatusRequest($request)
    {
        return $request->status;
    }

    public function getIdRequest($request)
    {
        return $request->id;
    }

    public function getMyRequest($userId)
    {
        $with = [
            'user:id,name,department_id',
            'manager:id,name',
            'category:id,name',
            'user.department:id,name'
        ];
        return $this->model->select(['*'])->with($with)
            ->where('user_id', $userId)->orderBy('id', 'DESC')->get();
    }

    public function changeStatus($idRequest, $status)
    {
        return $this->model->findOrFail($idRequest)->update(['status' => $status]);
    }


    public function getRequestDueDate()
    {
        $limit = Carbon::now()->addDay(2);
        return $this->model->with('manager:id,name,email', 'user:id,name')
            ->where('requests.status', '<>', config('constants.status.close'))
            ->where('due_date', '<=', $limit);
    }

    public function findWhereUserIdRequest($userId)
    {
        return $this->model->where('manager_id', $userId)->where('status', '!=', Config::get('constants.status.close'))
            ->pluck('id')->all();
    }

    public function changeUserIdRequest($ids)
    {
        return $this->model->whereIn('id', $ids)
            ->update(['manager_id' =>\config('constants.user.null')]);
    }

    public function nameRequest($ids)
    {
        return $this->model->whereIn('id', $ids)->pluck('name')->all();
    }

    public function changeManagerCategory($id, $manager_id)
    {
        return $this->model->where('category_id', $id)->where('manager_id', \config('constants.user.null'))
            ->update(['manager_id' => $manager_id]);
    }

    public function getArrayCategory($idCategory)
    {
        return $this->model->where('category_id', $idCategory)->pluck('id')->all();
    }
}
