<?php


namespace App\Services\PostbackService\Providers;

use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FinCPANetworkPostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        $params = [
            'click_id' => $user->transaction_id,
            'goal_id' => config('services.fin_cpa_network.goal_id'),
            'status' => config('services.fin_cpa_network.statuses.approved'),
            'transaction_id' => $user->unique_id,
        ];

        $baseUrl = 'https://adv.fincpanetwork.ru/add-conversion';
        $url = $baseUrl . '?' . http_build_query($params);

        $response = Http::get($url);

        Log::debug('Отправили информацию о постбэке в FinCPANetwork.', [
            'transaction_id' => $user->transaction_id,
            'request_url' => $url,
            'response' => $response,
        ]);
    }
}