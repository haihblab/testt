<?php

namespace App\Http\Requests\Api;

use App\Contracts\Repositories\UserRepositoryInterface;

class UserUpdateRequest extends ApiRequest
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'email' => [
                'required',
                'unique' => 'unique:users,email'
            ],
            'name' => [
                'required', 'max:191',
            ],
            'staff_id' => 'required',
            'role_id' => 'required',
            'department_id' => 'required',
        ];

        if ($this->method() == 'PUT') {
            $rules['email']['unique'] = $this->userRepository->listEmailUser($this->id);
        }
        return $rules;
    }
}
