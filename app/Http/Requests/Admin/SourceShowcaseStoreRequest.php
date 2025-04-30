<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SourceShowcaseStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('source_showcase_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
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
            'loan_offer_id' => [
                'required',
                'exists:loan_offers,id',
            ],
            'webmaster_id' => [
                'required',
                'exists:webmasters,id',
            ],
        ];
    }
}
