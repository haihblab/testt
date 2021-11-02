<?php

namespace App\Repositories;

use App\Contracts\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    public $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param string[] $columns
     * @param array $with
     * @return mixed
     */
    public function getColumns($columns = ['*'], $with = [])
    {
        return $this->model->select($columns)->with($with);
    }

    /**
     * @param $id
     * @param string[] $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function store($data)
    {
        return $this->model->create($data);
    }

    /**
     * @param Model $model
     * @param array $data
     * @return bool|mixed
     */
    public function update(Model $model, array $data)
    {
        return $model->update($data);
    }

    /**
     * @param Model $model
     * @return bool|mixed|null
     * @throws \Exception
     */
    public function destroy(Model $model)
    {
        return $model->delete();
    }

    /**
     * @param $ids
     * @return int|mixed
     */
    public function destroyMulti($ids)
    {
        return $this->model->destroy($ids);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function restoreSoftDelete($id)
    {
        return $this->model
            ->withTrashed()
            ->where('id', $id)
            ->restore();
    }
}
