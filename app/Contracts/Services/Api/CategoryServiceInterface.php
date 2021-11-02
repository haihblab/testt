<?php

namespace App\Contracts\Services\Api;

interface CategoryServiceInterface
{
    public function index($name);

    public function create($params);

    public function update($id, $params);

    public function destroy($id);

    public function show($id);

    public function delete($id);

    public function searchId($key);

    public function searchIdUpdate($key);
}
