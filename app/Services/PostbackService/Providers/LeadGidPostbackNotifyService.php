<?php


namespace App\Services\PostbackService\Providers;


use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadGidPostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * @var string
     */
    protected string $offerId;

    public function __construct()
    {
        $this->offerId = SettingsService::getByKey(SettingNameEnum::LeadGidOfferId);
    }

    /**
     * @param  User  $user
     */
    public function send(User $user): void
    {
        $params = [
            'offer_id' => $this->offerId,
            'adv_sub' => $user->unique_id,
            'transaction_id' => $user->transaction_id,
        ];

        $response = Http::get('https://go.leadgid.ru/aff_lsr', $params)
            ->json();

        Log::debug('Отправили информацию о постбэке в LeadGid.', [
            'transaction_id' => $user->transaction_id,
            'request_query' => $params,
            'response' => $response,
        ]);
    }
}