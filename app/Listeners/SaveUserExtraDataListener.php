<?php

namespace App\Listeners;

use App\Events\UserRegistrationFinished;
use App\Models\UserExtraData;

class SaveUserExtraDataListener
{
    public function __construct()
    {
    }

    public function handle(UserRegistrationFinished $event): void
    {
        $user = $event->user;
        
        $cookieData = $this->collectCookieData();
        
        if (empty($cookieData)) {
            dump('All cookies:', request()->cookie());
            dump('$_COOKIE global:', $_COOKIE);
            dd('Cookie data is empty');
            return;
        }

        UserExtraData::create([
            'user_id' => $user->id,
            'source' => 'adsfin',
            'site_id' => $cookieData['site_id'] ?? null,
            'place_id' => $cookieData['place_id'] ?? null,
            'banner_id' => $cookieData['banner_id'] ?? null,
            'campaign_id' => $cookieData['campaign_id'] ?? null,
            'click_id' => $cookieData['click_id'] ?? null,
            'webmaster_id' => $cookieData['webmaster_id'] ?? null,
            'raw_data' => $cookieData,
        ]);
    }

    private function collectCookieData(): array
    {
        $cookieNames = ['click_id', 'site_id', 'place_id', 'banner_id', 'campaign_id', 'webmaster_id'];
        $cookieData = [];

        foreach ($cookieNames as $cookieName) {
            $value = $_COOKIE[$cookieName];
            if ($value !== null) {
                $cookieData[$cookieName] = $value;
            }
        }

        return $cookieData;
    }
}