<?php

namespace App\Http\Requests\Api;

use App\Contracts\Repositories\CategoryRepositoryInterface;
use Illuminate\Validation\Rule;

class CategoryRequest extends ApiRequest
{
    protected $cateRepo;

    public function __construct(CategoryRepositoryInterface $cateRepo)
    {
        $this->cateRepo = $cateRepo;
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
                'unique' => 'unique:categories,name'
            ],
            'user_id' => ['required'],
            'status' => [
                'required'
            ],
        ];

        if ($this->method() == 'PUT') {
            $rules['name']['unique'] = $this->cateRepo->listNameCategory($this->id);
        }
        return $rules;
    }
}
