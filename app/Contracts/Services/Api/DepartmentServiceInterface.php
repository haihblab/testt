<?php

namespace App\Contracts\Services\Api;

interface DepartmentServiceInterface
{
    public function index($request);
    
    public function show($id);

    public function create($params);

    public function update($id, $params);
}
