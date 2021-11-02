<?php

namespace App\Repositories;

use App\Contracts\Repositories\CommentRepositoryInterface;
use App\Models\Comment;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    /**
     * UserRepository constructor.
     * @param Comment $comment
     */
    public function __construct(Comment $comment)
    {
        parent::__construct($comment);
    }

    public function getIdsComment($arr)
    {
        return $this->model->WhereIn('request_id', $arr)->pluck('id')->all();
    }

    public function getListComment($id, $perPage)
    {
        return $this->model->select(['*'])->with(['request:id,name', 'user:id,name'])
        ->orderby('id', 'DESC')
        ->where('request_id', $id)
        ->paginate($perPage);
    }
}
