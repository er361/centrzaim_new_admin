<?php
namespace App\Http\Requests\Admin;

use App\Enums\SmsTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SmsStoreRequest extends FormRequest
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
            'name' => [
                'required',
                'max:255',
            ],
            'type' => [
                'required',
                Rule::in(SmsTypeEnum::values()),
            ],
            'text' => [
                'required',
            ],
            'from' => [
                'nullable',
                'string',
                'max:255',
            ],
            'link' => [
                'nullable',
                'string',
                'max:255',
                'url',
            ],
            'delay' => [
                'required',
                'integer',
                'min:0',
            ],
            'is_enabled' => [
                'required',
                'in:0,1',
            ],
            'registered_after' => [
                'required',
                'date_format:d.m.Y H:i',
            ],
            'sms_provider_id' => [
                'required',
            ],
            'link_source_id' => [
                'nullable',
            ],
            'source_id' => [
                'nullable',
            ],
            'showcase_id' => [
                'nullable',
            ],
            'excluded_webmaster_id' => [
                'nullable',
                'array',
            ],
            'excluded_webmaster_id.*' => [
                'integer',
                'exists:webmasters,id',
            ],
            'included_webmaster_id' => [
                'nullable',
                'array',
            ],
            'included_webmaster_id.*' => [
                'integer',
                'exists:webmasters,id',
            ],
            'related_sms_id' => [
                'nullable',
                Rule::requiredIf(function () {
                    return $this->input('type') === SmsTypeEnum::AfterClick;
                }),
                'exists:sms,id',
            ],
        ];
    }
}
