<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class LoanOfferStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('loan_offer_create');
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'loan_id' => [
                'required',
                'exists:loans,id',
            ],
            'loan_offers' => [
                'required',
                'array',
            ],
            'loan_offers.*' => [
                'array'
            ],
            'loan_offers.*.*' => [
                'nullable',
                'exists:loan_links,id'
            ],
        ];
    }
}
