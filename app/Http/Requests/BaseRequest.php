<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'phone' => $this->sanitizePhoneNumber($this->input('phone')),
        ]);
    }

    /**
     * Get the sanitized phone number.
     *
     * @param string $phone
     * @return string
     */
    protected function sanitizePhoneNumber(string $phone): string
    {
        return preg_replace('/\D/', '', $phone);
    }
}
