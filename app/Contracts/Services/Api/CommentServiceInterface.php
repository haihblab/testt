<?php

namespace App\Contracts\Services\Api;

interface CommentServiceInterface
{
    public function create($params);

    public function index();

    public function listComment($id, $perPage);
}
