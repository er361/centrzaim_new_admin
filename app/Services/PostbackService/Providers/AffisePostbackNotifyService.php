<?php


namespace App\Services\PostbackService\Providers;

use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AffisePostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        $params = [
            'clickid' => $user->transaction_id,
            'action_id' => $user->unique_id,
            'status' => config('services.affise.statuses.approved'),
            'goal' => config('services.affise.goal'),
        ];

        $baseUrl = 'https://offers-social-market.affise.com/postback';
        $url = $baseUrl . '?' . http_build_query($params);

        $response = Http::get($url);

        Log::debug('Отправили информацию о постбэке в Affise.', [
            'transaction_id' => $user->transaction_id,
            'request_url' => $url,
            'response' => $response,
        ]);
    }
}