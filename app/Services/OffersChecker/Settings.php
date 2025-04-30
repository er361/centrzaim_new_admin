<?php

namespace App\Services\OffersChecker;

use Illuminate\Support\Facades\Storage;

class Settings
{
    public static int $YM_ID = 0;

    public static string $PLATFORM_ID = '1316606';

    public static array $OFFER_IDS = [
        140, 693, 711, 1354, 9153, 9863, 10164, 10387, 10945, 9560, 719, 11100,
        10445
    ];

    public static array $PUBLIC_OFFERS_IDS = [
        11100,// Займер VIP [micro][sale]
        693,// еКапуста[micro][status_lead]
        9560,// MoneyMan VIP [micro][sale]
        10695,// До зарплаты лесенка [micro][sale]
        10463,// А Де11333ньги [micro][sale]
        9863,// Max.Credit [micro][sale]
        10445,// 495 кредит
        10690,// Умные Наличные
        10946,// Finters [micro][sale]
        11087,// Простой вопрос [micro] [sale]
        10684,// Центрофинанс ONLINE-выдача VIP
        10523,// Срочно деньги
        718,// Е-заем [micro][sale]
        11333,// Да-кредит[micro][sale
        1044,// Turbozaim [micro][sale]
    ];

    public static function getOfferUrl(string $platform_id, array $urls): string
    {
        $offerUrlKey = array_search($platform_id, array_column($urls, 'platform_id'));
        return $urls[$offerUrlKey]['urls'][0];
    }

    public static function setOffersPosition(array &$offers, array $positions): void
    {
        usort($offers, function ($a, $b) use ($positions) {
            return array_search($a['id'], $positions) - array_search($b['id'], $positions);
        });
    }

    public static function getOffers()
    {
        $j = Storage::get('results.json');
        $data = json_decode($j, true);

        return $data['data'];
    }
}

