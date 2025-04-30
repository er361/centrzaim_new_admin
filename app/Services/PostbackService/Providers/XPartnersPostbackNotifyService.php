<?php


namespace App\Services\PostbackService\Providers;


use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XPartnersPostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * @var string
     */
    protected string $secure;

    /**
     * @var string
     */
    protected string $customField;

    public function __construct()
    {
        $this->secure = SettingsService::getByKey(SettingNameEnum::XPartnersToken);
        $this->customField = SettingsService::getByKey(SettingNameEnum::XPartnersCustomField);
    }

    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        $params = [
            'clickid' => $user->transaction_id,
            'action_id' => $user->unique_id,
            'secure' => $this->secure,
            'status' => config('services.x_partners.statuses.approved'),
            'custom_field1' => $this->customField,
        ];

        $baseUrl = 'https://offers-xpartners.affise.com/postback';
        $url = $baseUrl . '?' . http_build_query($params);

        $response = Http::get($url);

        Log::debug('Отправили информацию о постбэке в XPartners.', [
            'transaction_id' => $user->transaction_id,
            'request_url' => $url,
            'response' => $response,
        ]);
    }
}