<?php

namespace App\Http\Requests\Api\Users;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Api\ApiRequest;

class CreateRequest extends ApiRequest
{
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
    public function rules(): array
    {
        return [
            'email' => ['required','email',
                'unique' => 'unique:users,email'],
            'name' => [
                'required', 'max:191',
            ],
            'staff_id' => 'required',
            'role_id' => 'required',
            'department_id' => 'required'
        ];
        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.required' => 'email không được để trống',
            'email.email' => 'email phải là định dạng email',
            'name.required' => 'Tên không được để trống',
            'staff_id.required' => 'staff_id không được để trống',
            'role_id.required' => 'role_id không được để trống',
            'department_id.required' => 'department_id không được để trống'
        ];
    }
}
