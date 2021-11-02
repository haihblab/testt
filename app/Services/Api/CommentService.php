<?php

namespace App\Services\Api;

use App\Contracts\Repositories\CommentRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\Api\CommentServiceInterface;
use App\Mail\CommentEmail;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Services\AbstractService;
use Illuminate\Support\Facades\Mail;

class CommentService extends AbstractService implements CommentServiceInterface
{
    /**
     * @var CommentRepositoryInterface
     */
    protected $commentRepository;
    protected $requestRepository;
    protected $userRepository;

    /**
     * CommentService constructor.
     * @param CommentRepositoryInterface $commentRepository
     */
    public function __construct(
        CommentRepositoryInterface $commentRepository,
        RequestRepositoryInterface $requestRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->commentRepository = $commentRepository;
        $this->requestRepository = $requestRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param $params
     * @return array
     */
    public function index()
    {
        return $this->commentRepository
            ->getColumns(['*'], ['request:id,name', 'user:id,name'])
            ->orderby('id', 'DESC')
            ->paginate(10);
    }

    /**
     * @param $params
     * @return array
     */
    public function listComment($id, $perPage)
    {
        return [
            'code' => 200,
            'comments' => $this->commentRepository->getListComment($id, $perPage),
            'message' => 'Show list Comment'
        ];
    }

    /**
     * @param $params
     * @return array
     */
    public function create($params)
    {
        $user = JwtAuth::parseToken()->authenticate();
        $request = $this->requestRepository->find($params['request_id']);
        $comment = $this->commentRepository->store($params);
        $email[] = $user->email;
        $email[] = $this->userRepository->find($request->user_id)->email;
        $data = [
            'content' => $comment->content,
            'created_at' => $comment->created_at,
            'user' => $user->name,
            'request' => $request->name
        ];
        if (!empty($email)) {
            Mail::to($email)->send(new CommentEmail($data));
        } else {
            return [
                'code' => 201,
                'message' => 'Không có email người nhận'
            ];
        }
        return [
            'code' => 200,
            'comments' => [
                'id' => $comment->id,
                'request_id' => $comment->request_id,
                'content' => $comment->content,
                'created_at' => $comment->created_at,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name
                ]
            ],
            'message' => 'Thêm request thành công'
        ];
    }
}
