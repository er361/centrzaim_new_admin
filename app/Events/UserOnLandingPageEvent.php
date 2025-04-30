<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class UserOnLandingPageEvent
{
    use Dispatchable;

    public function __construct(
        public array $requestData
    )
    {

    }
}
