<?php


namespace App\Services\PostbackService\Providers;


use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuruLeadsPostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * Статус "Заявка принята" (конверсионное действие завершилось с успехом).
     */
    protected const int STATUS_ACCEPTED = 1;

    /**
     * @var string
     */
    protected string $secure;

    public function __construct()
    {
        $this->secure = SettingsService::getByKey(SettingNameEnum::GuruLeadsSecure);
    }

    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        $params = [
            'clickid' => $user->transaction_id,
            'goal' => 'loan',
            'status' => self::STATUS_ACCEPTED,
            'action_id' => $user->unique_id,
            'secure' => $this->secure,
        ];

        $baseUrl = 'http://offers.guruleads.ru/postback';
        $url = $baseUrl . '?' . http_build_query($params);

        $response = Http::get($url);

        Log::debug('Отправили информацию о постбэке в GuruLeads.', [
            'transaction_id' => $user->transaction_id,
            'request_url' => $url,
            'response' => $response,
        ]);
    }
}