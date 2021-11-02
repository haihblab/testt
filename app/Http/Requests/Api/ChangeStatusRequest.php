<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class ChangeStatusRequest extends ApiRequest
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
            'status' => 'required|integer|' . Rule::in(array_values(config('constants.status')))
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'Giá trị status không được để trống',
            'status.integer' => 'Giá trị status phải là số nguyên'
        ];
    }
}
