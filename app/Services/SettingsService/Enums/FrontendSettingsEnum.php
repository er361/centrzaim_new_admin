<?php

namespace App\Services\SettingsService\Enums;

enum FrontendSettingsEnum: string
{
    case IS_REDIRECT_ENABLED = 'is_redirect_enabled';
    case SLIDER_AMOUNT = 'slider_amount';
    case REDIRECT_URL = 'redirect_url';
    case REDIRECT_TIMING = 'redirect_timing';


    public function getLabel()
    {
        return match ($this) {
            self::IS_REDIRECT_ENABLED => 'Включить редирект',
            self::SLIDER_AMOUNT => 'Сумма на слайдере',
            self::REDIRECT_URL => 'URL для редиректа',
            self::REDIRECT_TIMING => 'Время до редиректа (миллисекунды)',
        };
    }

    public function getType(): string
    {
        return match ($this) {
            self::IS_REDIRECT_ENABLED => 'bool',
            self::SLIDER_AMOUNT => 'int',
            self::REDIRECT_URL => 'string',
            self::REDIRECT_TIMING => 'int',
        };
    }

    public static function getValidationRules(): array
    {
        return [
            self::IS_REDIRECT_ENABLED->value => 'boolean',
            self::SLIDER_AMOUNT->value => 'required|integer|min:0|max:100000',
            self::REDIRECT_URL->value => 'required|url',
            self::REDIRECT_TIMING->value => 'required|integer|min:0|max:100000',
        ];
    }

}
