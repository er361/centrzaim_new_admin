<?php


namespace App\Services\PostbackService\Providers;


use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadsPostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * Токен.
     * @var string
     */
    protected string $token;

    /**
     * Идентификатор цели.
     * @var string
     */
    protected string $goalId;

    public function __construct()
    {
        $this->token = SettingsService::getByKey(SettingNameEnum::LeadsToken);
        $this->goalId = SettingsService::getByKey(SettingNameEnum::LeadsGoalId);
    }

    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        $params = [
            'token' => $this->token,
            'goal_id' => $this->goalId,
            'transaction_id' => $user->transaction_id,
            'adv_sub' => $user->unique_id,
            'status' => 'approved',
        ];

        $response = Http::get('http://api.leads.su/advertiser/conversion/createUpdate', $params)
            ->json();

        Log::debug('Отправили информацию о постбэке в Leads.', [
            'transaction_id' => $user->transaction_id,
            'params' => $params,
            'response' => $response,
        ]);
    }
}