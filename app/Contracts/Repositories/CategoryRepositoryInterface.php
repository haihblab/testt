<?php

namespace App\Contracts\Repositories;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function paginates($litmit);

    public function listNameCategory($id);

    public function searchName($name);

    public function getIdCategory($category);

    public function searchIdUpdate($key);
    public function findWhereUserId($userId);

    public function changeUserIdCategory($ids);

    public function nameCategory($ids);
}
