<?php


namespace App\Http\Requests\Api;


use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'payment_card_number' => [
                'required',
                'string',
            ],
        ];
    }
}