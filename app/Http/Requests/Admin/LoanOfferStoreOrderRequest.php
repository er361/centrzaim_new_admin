<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class LoanOfferStoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('loan_offer_store_order');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'source_id' => [
                'required',
                'exists:sources,id',
            ],
            'showcase_id' => [
                'required',
                'exists:showcases,id',
            ],
            'loan_offers' => [
                'required',
                'array',
            ],
            'loan_offers.*' => [
                'required',
                Rule::exists('loan_offers', 'id')
                    ->where('source_id', $this->input('source_id'))
                    ->where('showcase_id', $this->input('showcase_id')),
            ],
        ];
    }
}
