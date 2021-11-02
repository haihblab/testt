<?php

namespace App\Contracts\Services\Api;

interface UserServiceInterface
{
    public function index($perPage, $filterParams);

    public function create($params);

    public function login($params);

    public function listUser();

    public function logout();

    public function listAdmin();

    public function update($id, $params);

    public function searchIdUpdate($key);

    public function getListRoleUpdate($user);

    public function getListDepartmentUpdate($user);

    public function show($id);
    public function getInfo();
    public function loginGoogle($access_token);

    public function test($role_id, $department_id, $status);
}
