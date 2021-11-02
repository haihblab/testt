<?php

namespace App\Contracts\Repositories;

interface ForgotPasswordRepositoryInterface extends BaseRepositoryInterface
{
    public function storeToken($params);
    public function findWhereEmail($email);
}
