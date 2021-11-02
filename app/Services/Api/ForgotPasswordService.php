<?php

namespace App\Services\Api;

use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Services\AbstractService;
use App\Notifications\ResetPassword;
use App\Contracts\Services\Api\ForgotPasswordServiceInterface;
use App\Contracts\Repositories\ForgotPasswordRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use Carbon\Carbon;

class ForgotPasswordService extends AbstractService implements ForgotPasswordServiceInterface
{
    /**
     * @var ForgotPasswordRepositoryInterface
     */
    protected $forgotPasswordRepository;
    protected $userRepository;

    /**
     * ForgotPasswordService constructor.
     * @param ForgotPasswordRepositoryInterface $forgotPasswordRepository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        ForgotPasswordRepositoryInterface $forgotPasswordRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->forgotPasswordRepository = $forgotPasswordRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function forgotPassword($params)
    {
        $params['token'] = Str::uuid(40);
        $data = $this->forgotPasswordRepository->storeToken($params);
        $data->notify(new ResetPassword($params['token'], $params['email']));
        return [
            'code' => 200,
            'message' => 'Gửi email reset password thành công'
        ];
    }

    /**
     * @param $params
     * @return mixed
     */
    public function resetPassword($params)
    {
        $data = $this->forgotPasswordRepository->findWhereEmail($params['email']);
        if ($data->token == $params['token']) {
            if (Carbon::now()->diffInMinutes($data->updated_at) < config('constants.GET_MINUTES_CHANGE_PASSWORD')) {
                $this->userRepository->resetPassword($params);
                return [
                    'code' => 200,
                    'message' => 'đổi password thành công !'
                ];
            }
            return [
                'code' => 201,
                'message' => 'Token đã hết hạn vui lòng làm mới lại !'
            ];
        }
        return [
            'code' => 201,
            'message' => 'Token không trùng khớp !'
        ];
    }
}
