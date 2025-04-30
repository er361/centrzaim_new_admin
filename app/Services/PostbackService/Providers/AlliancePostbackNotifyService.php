<?php


namespace App\Services\PostbackService\Providers;

use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AlliancePostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * @var string
     */
    protected string $token;

    /**
     * @var string
     */
    protected string $from;

    public function __construct()
    {
        $this->token = SettingsService::getByKey(SettingNameEnum::AllianceToken);
        $this->from = SettingsService::getByKey(SettingNameEnum::AllianceFrom);
    }

    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        $cost = $user->postbacks->first()?->cost ?? 0;

        $params = [
            'token' => $this->token,
            'click_id' => $user->transaction_id,
            'status' => 1, // Одобрено
            'from' => $this->from,
            'sub1' => $user->additional_transaction_id,
            'sum' => $cost,
        ];

        $baseUrl = 'https://alianscpa.ru/postback/get/partners';
        $url = $baseUrl . '?' . http_build_query($params);

        $response = Http::get($url);

        Log::debug('Отправили информацию о постбэке в Alliance.', [
            'transaction_id' => $user->transaction_id,
            'request_url' => $url,
            'response' => $response,
        ]);
    }
}