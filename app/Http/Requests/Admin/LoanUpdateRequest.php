<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class LoanUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('loan_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image' => [
                'nullable',
                'file',
                'max:10240', // 10 Мегабайт
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
            'rating' => [
                'nullable',
                'string',
                'max:255',
            ],
            'amount' => [
                'nullable',
                'string',
                'max:255',
            ],
            'issuing_time' => [
                'nullable',
                'string',
                'max:255',
            ],
            'issuing_period' => [
                'nullable',
                'string',
                'max:255',
            ],
            'issuing_bid' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}
