<?php

namespace App\Contracts\Services\Api;

interface ForgotPasswordServiceInterface
{
    public function forgotPassword($params);
    public function resetPassword($params);
}
