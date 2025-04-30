<?php


namespace App\Services\PostbackService\Providers;

use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Click2MoneyPostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * @var string
     */
    protected string $partner;

    public function __construct()
    {
        $this->partner = SettingsService::getByKey(SettingNameEnum::Click2MoneyPartner);
    }

    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        $params = [
            'cid' => $user->transaction_id,
            'partner' => $this->partner,
            'action' => 'approve',
            'lead_id' => $user->unique_id,
        ];

        $baseUrl = 'https://c2mpbtrck.com/cpaCallback';
        $url = $baseUrl . '?' . http_build_query($params);

        $response = Http::get($url);

        Log::debug('Отправили информацию о постбэке в Click2Money.', [
            'transaction_id' => $user->transaction_id,
            'request_url' => $url,
            'response' => $response,
        ]);
    }
}