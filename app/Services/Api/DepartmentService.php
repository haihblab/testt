<?php

namespace App\Services\Api;

use App\Contracts\Repositories\DepartmentRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\Api\DepartmentServiceInterface;
use App\Services\AbstractService;
use Illuminate\Support\Facades\Redis;

class DepartmentService extends AbstractService implements DepartmentServiceInterface
{
    /**
     * @var DepartmentRepositoryInterface
     */
    protected $departmentRepository;
    protected $userRepository;

    /**
     * DepartmentService constructor.
     * @param DepartmentRepositoryInterface $departmentRepository
     */
    public function __construct(
        DepartmentRepositoryInterface $departmentRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->departmentRepository = $departmentRepository;
        $this->userRepository = $userRepository;
    }

    public function index($request)
    {
        $department = $this->departmentRepository->getColumns()->get();
        return [
            'code' => 200,
            'department' => $department
        ];
    }

    public function show($id)
    {
        $department = $this->departmentRepository->find($id);
        return [
            'code' => 200,
            'department' => $department
        ];
    }

    public function create($params)
    {
        $name = $params['name'];
        $department = $this->departmentRepository->store($params);
        return [
            'code' => 200,
            'department' => $department,
        ];
    }

    public function update($params, $id)
    {
        Redis::flushDB();
        $array = $this->userRepository->getArrayDepartment($id);
        if ($params['status'] == \config('constants.statusCategory.disable') && !empty($array)) {
            return [
                'code' => 403,
                'message' => 'không thể disable do vẫn tồn tại user'

            ];
        } else {
            $department = $this->departmentRepository->update($this->departmentRepository->find($id), $params);
            $show = $this->departmentRepository->find($id);
            return [
                'code' => 200,
                'department' => $department,
                'show' => $show
            ];
        }
    }
}
