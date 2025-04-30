<?php


namespace App\Services\PostbackService\Providers;

use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadTargetPostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        $params = [
            'click_id' => $user->transaction_id,
            'application' => $user->unique_id,
            'status' => 'pending',
        ];

        $baseUrl = 'http://service.leadtarget.ru/postback/';
        $url = $baseUrl . '?' . http_build_query($params);

        $response = Http::get($url);

        Log::debug('Отправили информацию о постбэке в LeadTarget.', [
            'transaction_id' => $user->transaction_id,
            'request_url' => $url,
            'response' => $response,
        ]);
    }
}