<?php

namespace App\Services\Api;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Contracts\Repositories\CommentHistoryRepositoryInterface;
use App\Contracts\Repositories\CommentRepositoryInterface;
use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Contracts\Services\Api\CategoryServiceInterface;
use Illuminate\Support\Facades\Redis;
use App\Services\AbstractService;

class CategoryService extends AbstractService implements CategoryServiceInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;
    protected $requestRepository;
    protected $commentRepository;
    protected $historyRepository;

    /**
     * UserService constructor.
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        RequestRepositoryInterface $requestRepository,
        CommentRepositoryInterface $commentRepository,
        CommentHistoryRepositoryInterface $historyRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->requestRepository = $requestRepository;
        $this->commentRepository = $commentRepository;
        $this->historyRepository = $historyRepository;
    }

    /**
     * @param $params
     * @return array
     */
    public function index($name)
    {
        if ($name != "") {
            return $this->categoryRepository->searchName($name)->get();
        }
        return $this->categoryRepository->getColumns(['*'], ['user:id,name'])
            ->orderby('id', 'DESC')->get();
    }

    public function create($params)
    {
        return $this->categoryRepository->store($params);
    }

    public function update($id, $params)
    {
        Redis::flushDB();
        $array = $this->requestRepository->getArrayCategory($id);
        if ($params['status'] == \config('constants.statusCategory.disable') && !empty($array)) {
            return [
                'code' => 403,
                'message' => 'không thể disable do vẫn tồn tại request'
            ];
        } else {
            $categories = $this->categoryRepository->find($id);
            $this->categoryRepository->update($categories, $params);
            $this->requestRepository->changeManagerCategory($id, $params['user_id']);
            return [
                'code' => 200,
                'message' => 'sửa thành công'
            ];
        }
    }

    public function destroy($id)
    {
        return $this->categoryRepository->destroy($this->categoryRepository->find($id));
    }

    public function show($id)
    {
        return $this->categoryRepository->find($id);
    }

    public function delete($id)
    {
        $category = $this->categoryRepository->find($id);
        if ($category) {
            $idCategory = $this->categoryRepository->getIdCategory($category);
            $arr = [$idCategory];
            $idRequests = $this->requestRepository->getIdsRequest($arr);
            $idsComment = $this->commentRepository->getIdsComment($idRequests);
            $this->commentRepository->destroyMulti($idsComment);
            $this->requestRepository->destroyMulti($idRequests);
            return $this->categoryRepository->destroy($category);
        }
    }

    public function searchId($key)
    {
        return $this->categoryRepository->searchName($key)->first();
    }

    public function searchIdUpdate($key)
    {
        return $this->categoryRepository->searchIdUpdate($key)->first();
    }
}
