<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    /**
     * @param string[] $columns
     * @param array $with
     * @return mixed
     */
    public function getColumns($columns = ['*'], $with = []);

    /**
     * @param $id
     * @param string[] $columns
     * @return mixed
     */
    public function find($id, $columns = ['*']);

    /**
     * @param $data
     * @return mixed
     */
    public function store($data);

    /**
     * @param Model $model
     * @param array $data
     * @return mixed
     */
    public function update(Model $model, array $data);

    /**
     * @param Model $model
     * @return mixed
     */
    public function destroy(Model $model);

    /**
     * @param $ids
     * @return mixed
     */
    public function destroyMulti($ids);

    /**
     * @param $id
     * @return mixed
     */
    public function restoreSoftDelete($id);
}
