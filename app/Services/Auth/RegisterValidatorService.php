<?php

namespace App\Services\Auth;

use App\Services\AccountService\AccountSourceService;
use App\Services\SiteService;
use Illuminate\Support\Facades\Validator;
use App\Rules\MobilePhoneRule;

class RegisterValidatorService
{
    public function validate(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $config = SiteService::getActiveSiteConfiguration();
        $registerFields = $config['register_fields'];

        $registerFieldsRules = collect($registerFields)
            ->mapWithKeys(function (string $key) {
                return [$key => match ($key) {
                    'fullname' => ['required', 'string', 'max:255'],
                    'birthdate' => [
                        'nullable',
                        'date',
                        'before:today',
                        'after:01.01.1900',
                        'regex:/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/'
                    ],
                    'mphone' => ['required', 'string', 'max:255', 'unique:users', new MobilePhoneRule()],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    default => [],
                }];
            })->toArray();

        $rules = $registerFieldsRules;

        if (AccountSourceService::getSource() === null && $config['has_expanded_terms']) {
//            $rules += [
//                'terms_agree_1' => ['required', 'in:1'],
//                'terms_agree_2' => ['required', 'in:1'],
//                'terms_agree_3' => ['required', 'in:1'],
//                'terms_agree_4' => ['required', 'in:1'],
//            ];
        } else {
            $rules += ['terms_agree' => ['required', 'in:1']];
        }

        $rules = array_merge($rules, [
            'additional_terms_agree' => ['required', 'in:1'],
        ]);

        $data['mphone'] = $this->convertPhone($data['mphone'] ?? '');

        $make = Validator::make($data, $rules, [
            'terms_agree.required' => 'Пожалуйста, подтвердите согласие с условиями сервиса. Без этого продолжение оформления услуги невозможно.',
            'terms_agree.in' => 'Пожалуйста, подтвердите согласие с условиями сервиса. Без этого продолжение оформления услуги невозможно.',
            'additional_terms_agree.in' => 'Пожалуйста, подтвердите согласие на получение рекламно-информационных сообщений. Без этого продолжение оформления услуги невозможно.',
            'mphone.unique' => 'Пользователь с таким номером телефона уже существует.',
        ]);

        return $make;
    }

    protected function convertPhone(string $phone): string
    {
        return str_replace(['(', ')', '-', ' '], '', $phone);
    }
}
