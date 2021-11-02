<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\Api\UserServiceInterface;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Requests\Api\Users\IndexRequest;
use App\Http\Requests\Api\Users\CreateRequest;
use App\Http\Requests\Api\UserUpdateRequest;

class UserController extends ApiController
{
    protected $service;

    /**
     * UserController constructor.
     */
    public function __construct(UserServiceInterface $service)
    {
        $this->service = $service;
        parent::__construct();
    }

    /**
     * @param IndexRequest $request
     * @param CreateRequest $request
     * @param UserServiceInterface $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CheckAuthenticationException
     * @throws \App\Exceptions\CheckAuthorizationException
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\QueryException
     * @throws \App\Exceptions\ServerException
     * @throws \App\Exceptions\UnprocessableEntityException
     */
    public function index(IndexRequest $request)
    {
        $name = $request->get('name', '');
        $perPage = $request->get('per_page', 10);
        return $this->getData(function () use ($perPage, $name) {
            $arrUse = $this->service->index($perPage, $name);
            return [
                'code' => 200,
                'arrUser' => $arrUse
            ];
        });
    }

    public function create(CreateRequest $request)
    {
        $params = $request->except('password');
        return $this->doRequest(function () use ($params) {
            return $this->service->create($params);
        });
    }

    public function listAdmin()
    {
        return $this->getData(function () {
            return $this->service->listAdmin();
        });
    }

    public function listUser()
    {
        return $this->getData(function () {
            return $this->service->listUser();
        });
    }

    public function update(UserUpdateRequest $request, $id)
    {
        $params = $request->all();
        return $this->doRequest(function () use ($id, $params) {
            $updateuser = $this->service->update($id, $params);
            $show = $this->service->searchIdUpdate($id);
            return [
                'updateUser' => $updateuser,
                'show' => $show
            ];
        });
    }

    public function show($id)
    {
        return $this->getData(function () use ($id) {
            $user = $this->service->show($id);
            $departments = $this->service->getListDepartmentUpdate($user);
            $roles = $this->service->getListRoleUpdate($user);
            return [
                'code' => 200,
                'User' => $user,
                'department' => $departments,
                'role' => $roles
            ];
        });
    }
}
