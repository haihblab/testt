<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\Api\CategoryServiceInterface;
use App\Contracts\Services\Api\CommentHistoryServiceInterface;
use App\Http\Requests\Api\CommentHistoryRequest;

class CommentHistoryController extends ApiController
{
    /**
     * RequestController constructor.
     */
    protected $commentHistoryService;

    public function __construct(CommentHistoryServiceInterface $commentHistoryService)
    {
        $this->commentHistoryService = $commentHistoryService;
        parent::__construct();
    }

    /**
     * @param CommentHistoryRequest $request
     * @param CommentHistoryServiceInterface $commentHistoryService
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CheckAuthenticationException
     * @throws \App\Exceptions\CheckAuthorizationException
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\QueryException
     * @throws \App\Exceptions\ServerException
     * @throws \App\Exceptions\UnprocessableEntityException
     */

    public function index(CommentHistoryRequest $request)
    {
        $params = $request->all();
        return $this->getData(function () use ($params) {
            $arrRequest = $this->commentHistoryService->index($params);
            return $arrRequest;
        });
    }
}
