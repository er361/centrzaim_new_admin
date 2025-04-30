<?php


namespace App\Services\PostbackService\Providers;

use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadBitPostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * @var string
     */
    protected string $offerId;

    /**
     * @var string
     */
    protected string $advertiserId;

    protected string $urlPart;

    public function __construct()
    {
        $this->offerId = SettingsService::getByKey(SettingNameEnum::LeadBitOfferId);
        $this->advertiserId = SettingsService::getByKey(SettingNameEnum::LeadBitAdvertiserId);
        $this->urlPart = SettingsService::getByKey(SettingNameEnum::LeadBitUrlPart);
    }

    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        $params = [
            'status' => 'confirmed',
            'advertiser_id' => $this->advertiserId,
            'offer_id' => $this->offerId,
            'utid' => $user->transaction_id,
            'order_id' => $user->unique_id,
            'country' => 'RU',
            'target_id' => 1,
        ];

        $baseUrl = 'http://post.leadbit.biz/'.$this->urlPart;
        $url = $baseUrl . '?' . http_build_query($params);

        $response = Http::get($url);

        Log::debug('Отправили информацию о постбэке в LeadBit.', [
            'transaction_id' => $user->transaction_id,
            'request_url' => $url,
            'response' => $response,
        ]);
    }
}