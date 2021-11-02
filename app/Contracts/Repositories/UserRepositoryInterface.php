<?php

namespace App\Contracts\Repositories;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    //
    public function login($params);

    public function listAdmin();

    public function filter($filterParams);

    public function changeRoleIdManagerToUser($params);

    public function search($name);

    public function listNameUser($id);
    public function listEmailUser($id);

    public function searchIdUpdate($key);
    public function resetPassword($params);
    public function findWhereEmail($email);
    
    public function findManagerUser($department_id);

    public function getArrayDepartment($idDepartment);
}
