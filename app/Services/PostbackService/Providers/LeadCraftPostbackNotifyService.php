<?php


namespace App\Services\PostbackService\Providers;


use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadCraftPostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * @var string
     */
    protected string $token;

    /**
     * @var string
     */
    protected string $actionId;

    /**
     * @var int
     */
    protected int $conversionPrice;

    public function __construct()
    {
        $this->token = SettingsService::getByKey(SettingNameEnum::LeadCraftToken);
        $this->actionId = SettingsService::getByKey(SettingNameEnum::LeadCraftActionId);
        $this->conversionPrice = (int)SettingsService::getByKey(SettingNameEnum::LeadCraftConversionPrice);
    }

    /**
     * @param  User  $user
     */
    public function send(User $user): void
    {
        $params = [
            'token' => $this->token,
            'actionID' => $this->actionId,
            'status' => 'approved',
            'clickID' => $user->transaction_id,
            'advertiserID' => $user->unique_id,
            'reviseDate' => Carbon::now()->toDateString(),
            'price' => $this->conversionPrice,
        ];

        $baseUrl = 'https://api.leadcraft.ru/v1/advertisers/actions';
        $url = $baseUrl.'?'.http_build_query($params);

        $response = Http::get($url);

        Log::debug('Отправили информацию о постбэке в LeadCraft.', [
            'transaction_id' => $user->transaction_id,
            'request_url' => $url,
            'response' => $response,
        ]);
    }
}