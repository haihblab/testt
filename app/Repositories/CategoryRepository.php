<?php

namespace App\Repositories;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    /**
     * UserRepository constructor.
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        parent::__construct($category);
    }

    public function paginates($litmit)
    {

        return $this->model->with('User', 'Manager')->orderby('id', 'DESC')->paginate($litmit);
    }

    public function listNameCategory($id)
    {
        return Rule::unique('categories')->ignore($id);
    }

    public function searchName($key)
    {
        return $this->model->with('user:id,name')
            ->where('name', 'like', '%' . $key . '%');
    }

    public function getIdCategory($category)
    {
        return $category->id;
    }

    public function searchIdUpdate($key)
    {
        return $this->model->with('user:id,name')
            ->where('id', $key);
    }

    public function findWhereUserId($userId)
    {
        return $this->model->where('user_id', $userId)->pluck('id')->all();
    }

    public function changeUserIdCategory($ids)
    {
        return $this->model->whereIn('id', $ids)
            ->update(['user_id' => \config('constants.user.null')]);
    }

    public function nameCategory($ids)
    {
        return $this->model->whereIn('id', $ids)->pluck('name')->all();
    }
}
