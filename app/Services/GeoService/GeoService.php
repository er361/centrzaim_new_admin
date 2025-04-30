<?php

namespace App\Services\GeoService;

use App\Models\User;
use Illuminate\Support\Arr;
use MoveMoveIo\DaData\Enums\Language;
use MoveMoveIo\DaData\Facades\DaDataAddress;

class GeoService
{
    /**
     * Обновляет информацию о геолокации пользователя.
     * @param User $user
     * @return void
     */
    public function loadUserGeo(User $user): void
    {
        if (empty($user->ip_address)) {
            return;
        }

        if (!empty($user->geo_region) && !empty($user->geo_city)) {
            return;
        }

        /** @var array $ipData */
        $ipData = DaDataAddress::iplocate($user->ip_address, 1, Language::RU); // @phpstan-ignore-line

        $regionName = Arr::get($ipData, 'location.data.region', '');

        if (Arr::get($ipData, 'location.data.region_type_full', '') !== 'город') {
            $regionName .= ' ' . Arr::get($ipData, 'location.data.region_type_full', '');
        }

        $city = Arr::get($ipData, 'location.data.city_with_type');

        if (empty($city)) {
            $city = Arr::get($ipData, 'location.data.settlement_with_type', '');
        }

        $user->update([
            'geo_region' => $regionName,
            'geo_city' => $city,
        ]);
    }
}