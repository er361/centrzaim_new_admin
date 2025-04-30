<?php


namespace App\Services\PostbackService\Providers;


use App\Models\User;
use App\Services\PostbackService\PostbackNotifyServiceInterface;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadsTechPostbackNotifyService implements PostbackNotifyServiceInterface
{
    /**
     * @var string
     */
    protected string $goalId;

    public function __construct()
    {
        $this->goalId = SettingsService::getByKey(SettingNameEnum::LeadsTechGoalId);
    }

    /**
     * @param User $user
     */
    public function send(User $user): void
    {
        $params = [
            'click_id' => $user->transaction_id,
            'goal_id' => $this->goalId,
            'status' => config('services.leads_tech.statuses.approved'),
            'transaction_id' => $user->unique_id,
            'sumConfirm' => $user->webmaster->postback_cost ?? $user->webmaster->source->postback_cost,
        ];

        $baseUrl = 'https://offers.leads.tech/add-conversion';
        $url = $baseUrl . '?' . http_build_query($params);

        $response = Http::get($url);

        Log::debug('Отправили информацию о постбэке в LeadsTech.', [
            'transaction_id' => $user->transaction_id,
            'request_url' => $url,
            'response' => $response,
        ]);
    }
}