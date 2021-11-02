<?php

namespace App\Repositories;

use App\Contracts\Repositories\CommentHistoryRepositoryInterface;
use App\Models\CommentHistory;

class CommentHistoryRepository extends BaseRepository implements CommentHistoryRepositoryInterface
{
    /**
     * UserRepository constructor.
     * @param CommentHistory $commenthistory
     */
    public function __construct(CommentHistory $commenthistory)
    {
        parent::__construct($commenthistory);
    }
}
