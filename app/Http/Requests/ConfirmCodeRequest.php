<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ConfirmCodeRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        return $this->merge([
            'phone' => Str::remove(['(', ')', ' ', '-'], $this->input('phone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'code' => 'required|min:6|max:6',
            'phone' => 'required|string',
        ];
    }
}
