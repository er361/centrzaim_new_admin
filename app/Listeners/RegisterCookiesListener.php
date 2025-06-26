<?php

namespace App\Listeners;

use App\Events\UserOnLandingPageEvent;
use Illuminate\Support\Facades\Log;

class RegisterCookiesListener
{
    public function __construct()
    {
    }

    public function handle(UserOnLandingPageEvent $event): void
    {
        $requestData = collect($event->requestData);
        $sub5 = $requestData->has('aff_sub5') ? $requestData->get('aff_sub5') : null;
        cookie()->queue('aff_sub5', $sub5, 60 * 24 * 30);

    }
}
