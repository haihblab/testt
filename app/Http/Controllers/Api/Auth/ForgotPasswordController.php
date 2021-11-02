<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\ForgotPasswordRequest;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Contracts\Services\Api\ForgotPasswordServiceInterface;

class ForgotPasswordController extends ApiController
{
    /**
     * ForgotPasswordController constructor.
     */
    protected $forgotPasswordService;

    public function __construct(ForgotPasswordServiceInterface $forgotPasswordService)
    {
        parent::__construct();
        $this->forgotPasswordService = $forgotPasswordService;
    }

    /**
     * @param ForgotPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\CheckAuthenticationException
     * @throws \App\Exceptions\CheckAuthorizationException
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\QueryException
     * @throws \App\Exceptions\ServerException
     * @throws \App\Exceptions\UnprocessableEntityException
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $params = $request->all();
        return $this->doRequest(function () use ($params) {
            return $this->forgotPasswordService->forgotPassword($params);
        });
    }
}
