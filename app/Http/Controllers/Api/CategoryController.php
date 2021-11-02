<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\Api\CategoryServiceInterface;
use App\Contracts\Services\Api\UserServiceInterface;
use App\Http\Requests\Api\CategoryRequest;
use App\Http\Requests\Api\RequestRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class CategoryController extends ApiController
{
    /**
     * RequestController constructor.
     */
    protected $categoryService;
    protected $userService;

    public function __construct(
        CategoryServiceInterface $categoryService,
        UserServiceInterface $userService
    ) {
        $this->categoryService = $categoryService;
        $this->userService = $userService;
        parent::__construct();
    }

    /**
     * @param CategoryRequest $request
     * @param CategoryServiceInterface $categoryService
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CheckAuthenticationException
     * @throws \App\Exceptions\CheckAuthorizationException
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\QueryException
     * @throws \App\Exceptions\ServerException
     * @throws \App\Exceptions\UnprocessableEntityException
     */

    public function index(Request $request)
    {
        $name = $request->get('name', '');
        return $this->getData(function () use ($name) {
            $arrRequest = $this->categoryService->index($name);
            return [
                'code' => 200,
                'arrRequest' => $arrRequest
            ];
        });
    }

    public function create(CategoryRequest $request)
    {
        $params = $this->inforInput($request);
        $name = $params['name'];
        return $this->doRequest(function () use ($params, $name) {
            $this->categoryService->create($params);
            $show = $this->categoryService->searchId($name);
            return [
                'code' => 200,
                'show' => $show
            ];
        });
    }

    public function update(CategoryRequest $request, $id)
    {
        $params = $this->inforInput($request);
        return $this->doRequest(function () use ($id, $params) {
            $updateCategory = $this->categoryService->update($id, $params);
            $show = $this->categoryService->searchIdUpdate($id);
            return [
                'updateCategory' => $updateCategory,
                'show' => $show
            ];
        });
    }

    public function destroy($id)
    {
        return $this->doRequest(function () use ($id) {
            return $this->categoryService->destroy($id);
        });
    }

    public function show($id)
    {
        return $this->getData(function () use ($id) {
            $category = $this->categoryService->show($id);
            $userId = $category->user->id;
            $userName = $category->user->name;
            $usersObject = $this->userService->listUser();
            $users = [];
            $users[$userId] = $userName;
            foreach ($usersObject as $userObject) {
                $users[$userObject->id] = $userObject->name;
            }
            return [
                'code' => 200,
                'category' => $category,
                'users' => $users,
            ];
        });
    }

    public function inforInput($request)
    {
        $params = $request->except('status');
        $status = $request->get('status');
        $statusEnable = Config::get('constants.statusCategory.enable');
        $statusDisable = Config::get('constants.statusCategory.disable');
        if ($status == $statusEnable) {
            $params['status'] = $statusEnable;
        } elseif ($status == $statusDisable) {
            $params['status'] = $statusDisable;
        }
        return $params;
    }

    public function delete($id)
    {
        return $this->doRequest(function () use ($id) {
            $deleteCategory = $this->categoryService->delete($id);
            return [
                'code' => 200,
                'deleteCategory' => $deleteCategory
            ];
        });
    }
}
