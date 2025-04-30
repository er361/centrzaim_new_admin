<?php

namespace App\Http\Requests\Admin;

use App\Models\Banner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class BannerStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('banner_create');
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
                'string',
                'max:255',
            ],
            'position' => [
                'required',
                'string',
                Rule::in(
                    array_keys(Banner::POSITIONS)
                ),
            ],
            'placement_id' => [
                'required',
                'string',
                'max:255',
            ],
            'code' => [
                'required',
                'string',
                'max:65535',
            ],
            'webmaster_id' => [
                'nullable',
                'array',
            ],
            'webmaster_id.*' => [
                Rule::exists('webmasters', 'id'),
            ],
            'source_id' => [
                'nullable',
                'array',
            ],
            'source_id.*' => [
                Rule::exists('sources', 'id'),
            ],
        ];
    }
}
