<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Http\Controllers\Api\ApiController;
use App\Contracts\Services\Api\ForgotPasswordServiceInterface;
use Illuminate\Http\Request;
use App\Http\Requests\Api\ResetPasswordRequest;

class ResetPasswordController extends ApiController
{
    /**
     * ResetPasswordController constructor.
     */
    protected $forgotPasswordService;

    public function __construct(ForgotPasswordServiceInterface $forgotPasswordService)
    {
        parent::__construct();
        $this->forgotPasswordService = $forgotPasswordService;
    }

    /**
     * @param ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CheckAuthenticationException
     * @throws \App\Exceptions\CheckAuthorizationException
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\QueryException
     * @throws \App\Exceptions\ServerException
     * @throws \App\Exceptions\UnprocessableEntityException
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $params = $request->only('email', 'token', 'password', 'password_confirmation');
        return $this->getData(function () use ($params) {
            return $this->forgotPasswordService->resetPassword($params);
        });
    }
}
