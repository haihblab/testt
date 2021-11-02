<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\PasswordReset;
use Illuminate\Validation\Rule;
use App\Contracts\Repositories\ForgotPasswordRepositoryInterface;

class ForgotPasswordRepository extends BaseRepository implements ForgotPasswordRepositoryInterface
{
    /**
     * ForgotPasswordRepository constructor.
     * @param PasswordReset $category
     */
    public function __construct(PasswordReset $passwordReset)
    {
        parent::__construct($passwordReset);
    }

    public function storeToken($params)
    {
        return $this->model->updateOrCreate(
            ['email' => $params['email']],
            ['token' => $params['token'], 'created_at' => Carbon::now()]
        );
    }

    public function findWhereEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }
}
