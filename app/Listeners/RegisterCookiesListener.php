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

        $this->handleAdsfinCookies($requestData);
    }

    private function handleAdsfinCookies($requestData): void
    {
        $sources = config('services.sources', []);
        
        // Find AdsFin configuration by source_id
        $adsfinConfig = null;
        foreach ($sources as $source) {
            if (isset($source['source_id']) && $source['source_id'] === \App\Models\Source::ID_ADSFIN) {
                $adsfinConfig = $source;
                break;
            }
        }

        if (!$adsfinConfig || !isset($adsfinConfig['cookie_mapping'])) {
            Log::warning('AdsFin cookies configuration not found');
            return;
        }

        $cookieLifetime = $adsfinConfig['cookie_lifetime'] ?? (31 * 24 * 60 * 60);
        $cookieLifetimeMinutes = $cookieLifetime / 60;

        foreach ($adsfinConfig['cookie_mapping'] as $requestParam => $cookieName) {
            if ($requestData->has($requestParam)) {
                $value = $requestData->get($requestParam);
                cookie()->queue($cookieName, $value, $cookieLifetimeMinutes);
            }
        }
    }
}
