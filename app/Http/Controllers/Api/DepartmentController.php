<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\Api\DepartmentServiceInterface;
use App\Http\Requests\Api\DepartmentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class DepartmentController extends ApiController
{
    /**
     * DepartmentController constructor.
     */
    protected $departmentService;

    public function __construct(
        DepartmentServiceInterface $departmentService
    ) {
        $this->departmentService = $departmentService;
        parent::__construct();
    }

    /**
     * @param DepartmentRequest $request
     * @param DepartmentServiceInterface $categoryService
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
        return $this->getData(function () use ($request) {
            return $this->departmentService->index($request);
        });
    }

    public function show($id)
    {
        return $this->getData(function () use ($id) {
            $department = $this->departmentService->show($id);
            return $department;
        });
    }

    public function create(DepartmentRequest $request)
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            $department = $this->departmentService->create($params);
            return $department;
        });
    }

    public function update(DepartmentRequest $request, $id)
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params, $id) {
            $department = $this->departmentService->update($params, $id);
            return $department;
        });
    }
}
