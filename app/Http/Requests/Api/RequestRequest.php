<?php

namespace App\Http\Requests\Api;

use App\Contracts\Repositories\RequestRepositoryInterface;
use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class RequestRequest extends ApiRequest
{
    protected $requestRepo;

    public function __construct(RequestRepositoryInterface $requestRepo)
    {
        $this->requestRepo = $requestRepo;
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
        $now = date('Y-m-d');
        $rules = [
            'name' => [
                'required', 'max:191',
            ],
            'user_id' => [],
            'manager_id' => ['required'],
            'status' => [
                'max:191'
            ],
            'category_id' => ['required'],
            'due_date' => ['date_format:"Y-m-d"', 'after_or_equal:' . $now],
            'content' => [
                'required', 'max:255'
            ],
            'priority' => [
                'numeric'
            ]
        ];
        return $rules;
    }
}
