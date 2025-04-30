<?php

namespace App\Faker;

use Faker\Provider\Base;

class MobilePhoneProvider extends Base
{
    public function mobilePhone(): string
    {
        return '+7 ('
            . $this->generator->randomNumber(3, true)
            . ') '
            . $this->generator->randomNumber(3, true)
            . '-'
            . $this->generator->randomNumber(2, true)
            . '-'
            . $this->generator->randomNumber(2, true);
    }
}