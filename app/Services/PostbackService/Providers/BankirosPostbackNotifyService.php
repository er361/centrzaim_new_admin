<?php


namespace App\Services\PostbackService\Providers;

use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BankirosPostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * @var string
     */
    protected string $siteId;

    public function __construct()
    {
        $this->siteId = SettingsService::getByKey(SettingNameEnum::BankirosSiteId);
    }

    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        $params = [
            'aff' => $user->transaction_id,
            'type' => 'img',
            'conversion' => $user->unique_id,
            'status' => 0,
        ];

        $baseUrl = 'https://tracker.myfin.group/api/orders/'.$this->siteId;
        $url = $baseUrl . '?' . http_build_query($params);

        $response = Http::get($url);

        Log::debug('Отправили информацию о постбэке в Bankiros.', [
            'transaction_id' => $user->transaction_id,
            'request_url' => $url,
            'response' => $response,
        ]);
    }
}