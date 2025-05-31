<?php

namespace App\Services\PostbackService\Providers;

use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdsFinPostbackNotifyService implements PostbackNotifyServiceInterface
{

    const LEAD_CONVERION = 1;


    public function send(User $user): void
    {
        $cost = $user->postbacks()->first()?->cost ?? 0;

        $params = [
            'conversion' => self::LEAD_CONVERION,
            'sum' => $cost,
            'click_id' => $user->transaction_id,
        ];

        $baseUrl = 'https://reg.adsfin.net/postback';
        $url = $baseUrl . '?' . http_build_query($params);

        $response = Http::get($url);

        Log::debug('Отправили информацию о постбэке в AdsFin.', [
            'transaction_id' => $user->transaction_id,
            'request_url' => $url,
            'response' => $response,
        ]);
    }
}