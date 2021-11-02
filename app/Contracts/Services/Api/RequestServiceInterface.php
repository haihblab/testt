<?php

namespace App\Contracts\Services\Api;

interface RequestServiceInterface
{
    public function index($per_page, $params, $page);
    public function show($id);
    public function create($params);
    public function update($params, $id);
    public function delete($id);
    public function showMyRequest($userId);
    public function changeStatus($idRequest, $status);
    public function getRequestDueDate();
}
