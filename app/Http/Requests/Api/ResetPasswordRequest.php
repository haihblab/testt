<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Api\ApiRequest;

class ResetPasswordRequest extends ApiRequest
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
    public function rules()
    {
        return [
            'email' => 'required|exists:password_resets,email',
            'token' => 'required|exists:password_resets,token',
            'password' => 'required|min:6|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'email không để trống',
            'token.required' => 'token không để trống',
            'email.exists' => 'email không tồn tại',
            'token.exists' => 'token không tồn tại',
            'password.required' => 'password mới không đưuọc để trống',
            'password.min' => 'password mới độ dài phải lớn hơn 6',
            'password.confirmed' => 'mật khẩu không trùng nhau',
        ];
    }
}
