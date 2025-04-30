<?php


namespace App\Services\PostbackService\Providers;

use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FinkortPostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        $cost = $user->postbacks->first()?->cost ?? 0;

        $params = [
            'click_id' => $user->transaction_id,
            'wm_id' => $user->webmaster->api_id,
            'status' => config('services.finkort.statuses.approved'),
            'action' => config('services.finkort.action'),
            'sum' => $cost,
        ];

        $baseUrl = 'https://lk.finkort.ru/api/offer/finkort/postback';
        $url = $baseUrl . '?' . http_build_query($params);

        $response = Http::get($url);

        Log::debug('Отправили информацию о постбэке в Finkort.', [
            'transaction_id' => $user->transaction_id,
            'request_url' => $url,
            'response' => $response,
        ]);
    }
}