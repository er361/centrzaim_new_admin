<?php

namespace App\Http\Requests;

use App\Models\Showcase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class LoanWebmasterOfferStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('loan_offer_create');
    }

    public function rules(): array
    {
        return [
            'loan_id' => 'required|integer|exists:loans,id',
            'loan_offers' => 'required|array',
            'loan_offers.*.webmaster_id' => 'required|integer|exists:webmasters,id',

            // `showcases` должен быть массивом
            'loan_offers.*.showcases' => 'required|array',

            // Валидация ключа (showcase_id) и значения (loan_link_id)
            'loan_offers.*.showcases.*' => [
                'nullable',
                'integer',
                Rule::exists('loan_links', 'id'), // Значение должно существовать в loan_links.id
            ],
            'loan_offers.*.showcases' => [
                function ($attribute, $value, $fail) {
                    foreach ($value as $showcaseId => $loanLinkId) {
                        // Проверяем, существует ли showcase_id в базе
                        if (!Showcase::where('id', $showcaseId)->exists()) {
                            $fail("Витрина $showcaseId не найден в базе.");
                        }
                    }
                }
            ],
        ];
    }
}
