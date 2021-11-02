<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\Api\CommentServiceInterface;
use App\Http\Requests\Api\CommentRequest;
use Tymon\JWTAuth\Facades\JWTAuth;

class CommentController extends ApiController
{
    /**
     * RequestController constructor.
     */
    protected $commentService;

    public function __construct(CommentServiceInterface $commentService)
    {
        $this->commentService = $commentService;
        parent::__construct();
    }

    /**
     * @param CommentRequest $request
     * @param CommentServiceInterface $commentService
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CheckAuthenticationException
     * @throws \App\Exceptions\CheckAuthorizationException
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\QueryException
     * @throws \App\Exceptions\ServerException
     * @throws \App\Exceptions\UnprocessableEntityException
     */

    public function index()
    {
        return $this->getData(function () {
            return $this->commentService->index();
        });
    }

    public function listComment($id, CommentRequest $request)
    {
        $perPage = $request->get('per_page', 5);
        return $this->getData(function () use ($id, $perPage) {
            return $this->commentService->listComment($id, $perPage);
        });
    }

    public function create(CommentRequest $request)
    {
        $params = $request->all();
        $params['title'] = 'comment';
        $params['user_id'] = JWTAuth::parseToken()->authenticate()->id;
        return $this->doRequest(function () use ($params) {
            return $this->commentService->create($params);
        });
    }
}
