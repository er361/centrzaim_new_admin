<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebmasterTemplateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'source_id' => 'required|integer|exists:sources,id',
            'showcase_id' => 'required|integer|exists:showcases,id',
            'webmaster_id' => 'nullable|integer|exists:webmasters,id',
        ];
    }
}
