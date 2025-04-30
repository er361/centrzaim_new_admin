<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class SendSmsRequest extends FormRequest
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
            'phone' => 'required|string',
        ];
    }
}
