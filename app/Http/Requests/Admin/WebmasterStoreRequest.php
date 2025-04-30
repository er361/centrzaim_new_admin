<?php
namespace App\Http\Requests\Admin;

use App\Services\PostbackService\PostbackServiceStepDecider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebmasterStoreRequest extends FormRequest
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
            'source_id' => [
                'required',
                'numeric',
            ],
            'api_id' => [
                'required',
                'string',
                Rule::unique('webmasters')
                    ->where(fn ($query) => $query->where('source_id', $this->input('source_id'))),
                'max:255',
            ],
            'postback_cost' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'page_tag' => [
                'nullable',
                'string',
                'max:65535',
            ],
            'comment' => [
                'nullable',
                'string',
                'max:65535',
            ],
            'is_payment_required' => [
                'in:0,1',
            ],
            'income_percent'=>[
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'postback_step' => [
                'nullable',
                'string',
                Rule::in(
                    array_keys(PostbackServiceStepDecider::STEPS)
                ),
            ],
        ];
    }
}
