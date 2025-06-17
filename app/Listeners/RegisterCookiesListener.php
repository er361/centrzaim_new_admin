<?php

namespace App\Listeners;

use App\Events\UserOnLandingPageEvent;

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

        $this->handleAdsfinCookies($requestData);
    }

    private function handleAdsfinCookies($requestData): void
    {
        $adsfinConfig = config('services.sources.0.cookie_mapping');
        
        if (!$adsfinConfig) {
            return;
        }

        $cookieLifetime = config('services.sources.0.cookie_lifetime', 31 * 24 * 60 * 60);
        $cookieLifetimeMinutes = $cookieLifetime / 60;

        foreach ($adsfinConfig as $requestParam => $cookieName) {
            if ($requestData->has($requestParam)) {
                $value = $requestData->get($requestParam);
                cookie()->queue($cookieName, $value, $cookieLifetimeMinutes);
            }
        }
    }
}
