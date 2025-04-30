<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MobilePhoneRule implements Rule
{
    /**
     * @var string[]
     */
    protected array $regions;

    /**
     * MobilePhoneRule constructor.
     */
    public function __construct()
    {
        $this->regions = config('phones.regions.ru');
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if ($value === null) {
            return true;
        }

        $pattern = '/\+7\d{10}/m';

        if (!preg_match($pattern, $value)) {
            return false;
        }

        $regionCode = substr($value, 2, 3);

        if (!in_array($regionCode, $this->regions)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Неверный формат номера телефона. Поддерживаются только номера российских операторов.';
    }
}