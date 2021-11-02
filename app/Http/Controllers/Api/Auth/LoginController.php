<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Users\LoginRequest;
use App\Contracts\Services\Api\UserServiceInterface;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;

class LoginController extends ApiController
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
        parent::__construct();
    }

    public function login(LoginRequest $request)
    {
        $params = $request->only('email', 'password');
        return $this->getData(function () use ($params) {
            return $this->userService->login($params);
        });
    }

    public function logout()
    {
        return $this->getData(function () {
            return $this->userService->logout();
        });
    }

    public function getInfo()
    {
        return $this->getData(function () {
            return $this->userService->getInfo();
        });
    }

    public function loginGoogle(Request $request)
    {
        $access_token = $request->access_token;
        return $this->doRequest(function () use ($access_token) {
            return $this->userService->loginGoogle($access_token);
        });
    }
}
