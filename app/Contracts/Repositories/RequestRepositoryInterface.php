<?php

namespace App\Contracts\Repositories;

interface RequestRepositoryInterface extends BaseRepositoryInterface
{
    public function paginates($litmit);

    public function filter($params, $per_page);

    public function listNameRequest($id);

    public function getIdsRequest($arr);

    public function getStatusRequest($request);

    public function getIdRequest($request);

    public function getMyRequest($userId);

    public function changeStatus($idRequest, $status);
    public function getRequestDueDate();

    public function findWhereUserIdRequest($userId);

    public function changeUserIdRequest($ids);

    public function nameRequest($ids);

    public function changeManagerCategory($id, $manager_id);

    public function getArrayCategory($idCategory);
}
