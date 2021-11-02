<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\Api\RequestServiceInterface;
use App\Http\Requests\Api\RequestRequest;
use App\Http\Requests\Api\ChangeStatusRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Facades\JWTAuth;

class RequestController extends ApiController
{
    /**
     * RequestController constructor.
     */
    protected $requestService;

    public function __construct(RequestServiceInterface $requestService)
    {
        $this->requestService = $requestService;
        parent::__construct();
    }

    /**
     * @param RequestRequest $request
     * @param RequestServiceInterface $requestService
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CheckAuthenticationException
     * @throws \App\Exceptions\CheckAuthorizationException
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\QueryException
     * @throws \App\Exceptions\ServerException
     * @throws \App\Exceptions\UnprocessableEntityException
     */

    public function index(Request $request)
    {
        $params['name_request'] = $request->get('name_request', '');
        $params['content_request'] = $request->get('content_request', '');
        $params['due_date_request'] = $request->get('due_date_request', '');
        $params['status'] = [$request->get('status', '')];
        $params['author_request'] = $request->get('author_request', '');
        $params['assign_request'] = $request->get('assign_request', '');
        $params['category_request'] = $request->get('category_request', '');
        $params['department_id'] = $request->get('department_id', '');
        $per_page = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        return $this->getData(function () use ($per_page, $params, $page) {
            return $this->requestService->index($per_page, $params, $page);
        });
    }

    public function show($id)
    {
        return $this->getData(function () use ($id) {
            return $this->requestService->show($id);
        });
    }

    public function showMyRequest()
    {
        $userId = JWTAuth::parseToken()->authenticate()->id;
        return $this->getData(function () use ($userId) {
            return $this->requestService->showMyRequest($userId);
        });
    }

    public function create(RequestRequest $request)
    {
        $params = $request->except('user_id', 'status');
        $params['user_id'] = JWTAuth::parseToken()->authenticate()->id;
        $params['status'] = Config::get('constants.status.open');
        return $this->doRequest(function () use ($params) {
            return $this->requestService->create($params);
        });
    }

    public function update(RequestRequest $request, $id)
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params, $id) {
            return $this->requestService->update($params, $id);
        });
    }

    public function delete($id)
    {
        return $this->doRequest(function () use ($id) {
            $deleteRequest = $this->requestService->delete($id);
            return [
                'code' => 200,
                'deleteRequest' => $deleteRequest
            ];
        });
    }

    public function changeStatus(ChangeStatusRequest $request, $id)
    {
        $status = $request->status;
        return $this->doRequest(function () use ($id, $status) {
            return $this->requestService->changeStatus($id, $status);
        });
    }
}
