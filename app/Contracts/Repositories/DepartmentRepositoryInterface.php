<?php

namespace App\Contracts\Repositories;

interface DepartmentRepositoryInterface extends BaseRepositoryInterface
{
    public function listNameDepartment($id);
}
