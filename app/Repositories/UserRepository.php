<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Config;
use App\Models\User;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Exceptions\CheckAuthenticationException;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function login($params)
    {
        $params['status'] = config('constants.statusUser.enable');
        if (!$token = JWTAuth::attempt($params)) {
            return ['code' => 500, 'message' => 'sai tài khoản mật khẩu !'];
        }
        $user = JWTAuth::user();
        return [
            'code' => 200,
            'data' => [
                'user' => $user,
                'token_type' => 'Bearer',
                'access_token' => $token,
            ]
        ];
    }

    public function listAdmin()
    {
        return $this->model->where('role_id', Config::get('constants.GET_ROLE_ID.Admin'))
            ->where('status', Config::get('constants.statusUser.enable'));
    }

    public function filter($filterParams)
    {
        return $this->model->with('role:id,name', 'department:id,name')
            ->when($filterParams, function ($query, $filterParams) {
                return $query->where('name', 'like', "%$filterParams%");
            });
    }

    public function changeRoleIdManagerToUser($params)
    {
        return $this->model->where('department_id', $params['department_id'])
            ->where('role_id', Config::get('constants.GET_ROLE_ID.Manager'))
            ->update(['role_id' => Config::get('constants.GET_ROLE_ID.User')]);
    }

    public function search($name)
    {
        return $this->model->select('users.*')->with('role:id,name', 'department:id,name')
            ->join('departments', 'departments.id', '=', 'users.department_id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->orWhere('departments.name', 'like', '%' . $name . '%')
            ->orWhere('roles.name', 'like', '%' . $name . '%')
            ->orwhere('users.name', 'like', '%' . $name . '%')
            ->orwhere('users.email', 'like', '%' . $name . '%')
            ->orwhere('users.staff_id', $name);
    }

    public function listNameUser($id)
    {
        return Rule::unique('users', 'name')->ignore($id);
    }

    public function listEmailUser($id)
    {
        return Rule::unique('users', 'email')->ignore($id);
    }

    public function searchIdUpdate($key)
    {
        return $this->model->with('role:id,name', 'department:id,name')
            ->where('id', $key);
    }

    public function resetPassword($params)
    {
        return $this->model->where('email', $params['email'])
            ->update(['password' => Hash::make($params['password'])]);
    }

    public function findWhereEmail($email)
    {
        return $this->model->where('email', $email);
    }

    public function findManagerUser($department_id)
    {
        return $this->model->select('users.name', 'users.id')
            ->where('role_id', config('constants.GET_ROLE_ID.Manager'))
            ->where('department_id', $department_id)->first();
    }

    public function getArrayDepartment($idDepartment)
    {
        return $this->model->where('department_id', $idDepartment)->pluck('id')->all();
    }
}
