<?php

namespace App\Http\Requests\Api;

use App\Contracts\Repositories\DepartmentRepositoryInterface;

class DepartmentRequest extends ApiRequest
{
    protected $departmentRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
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
            'name' => [
                'required', 'max:191',
                'unique' => 'unique:departments,name'
            ],
            'status' => [
                'required'
            ],
        ];

        if ($this->method() == 'PUT') {
            $rules['name']['unique'] = $this->departmentRepository->listNameDepartment($this->id);
        }
        return $rules;
    }
}
