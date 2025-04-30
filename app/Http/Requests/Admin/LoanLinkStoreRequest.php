<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class LoanLinkStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('loan_link_create');
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
            'loan_links' => [
                'required',
                'array',
            ],
            'loan_links.*' => [
                'nullable',
                'string',
                'max:255',
                'url',
            ],
            'source_ids' => [
                'exists:sources,id',
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'source_ids' => array_keys($this->get('loan_links'))
        ]);
    }
}
