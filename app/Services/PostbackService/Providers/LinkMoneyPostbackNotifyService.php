<?php


namespace App\Services\PostbackService\Providers;


use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LinkMoneyPostbackNotifyService implements PostbackNotifyServiceInterface
{
    protected string $key;

    public function __construct()
    {
        $this->key = SettingsService::getByKey(SettingNameEnum::LinkMoneyKey);
    }

    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        $params = [
            'status' => 'approved',
            'key' => $this->key,
            'id_click' => $user->transaction_id,
        ];

        $baseUrl = 'https://agn-item.ru/psb';
        $url = $baseUrl . '?' . http_build_query($params);

        $response = Http::get($url);

        Log::debug('Отправили информацию о постбэке в ЛинкМани.', [
            'transaction_id' => $user->transaction_id,
            'request_url' => $url,
            'response' => $response,
        ]);
    }
}