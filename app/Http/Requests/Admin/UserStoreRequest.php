<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('user_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
            ],
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
            'accessible_webmaster_id' => [
                'nullable',
                'array',
            ],
            'accessible_webmaster_id.*' => [
                'integer',
                'exists:webmasters,id',
            ],
        ];
    }
}
