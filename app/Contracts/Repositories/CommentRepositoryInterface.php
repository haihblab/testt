<?php

namespace App\Contracts\Repositories;

interface CommentRepositoryInterface extends BaseRepositoryInterface
{
    public function getIdsComment($arr);

    public function getListComment($id, $perPage);
}
