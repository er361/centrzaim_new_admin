<?php

namespace App\Services\LeadService;

use App\Models\User;
use App\Services\GeoService\GeoService;
use App\Services\LeadService\Exceptions\UserDuplicateException;
use App\Services\LeadService\Exceptions\UserNotEligibleException;
use App\Services\LeadService\Exceptions\UserSendException;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class LeadServiceLeads implements LeadServiceContract
{
    /**
     * Идентификатор площадки.
     * @var int
     */
    protected int $platformId;

    /**
     * Название сайта.
     * @var string
     */
    protected string $siteName;

    /**
     * Токен для API.
     * @var string
     */
    protected string $apiToken;

    /**
     * @var GeoService
     */
    protected GeoService $geoService;

    /**
     * LeadServiceLeads constructor.
     */
    public function __construct()
    {
        $this->platformId = (int)SettingsService::getByKey(SettingNameEnum::LeadsPlatformId);
        $this->apiToken = SettingsService::getByKey(SettingNameEnum::LeadsApiToken);

        $this->siteName = config('app.name');
        $this->geoService = App::make(GeoService::class);
    }

    /**
     * Отправляет информацию о пользователе.
     * @param User $user
     * @return void
     */
    public function send(User $user): void
    {
        $this->geoService->loadUserGeo($user);

        $baseUrl = 'http://api.leads.su/webmaster/leads/push';

        $data = [
            'token' => $this->apiToken,
            'platform_id' => $this->platformId,
            'offer_id' => $this->getOfferId(),
            'lead_time' => $user->created_at->toDateTimeString(),
            'source' => $this->siteName,
            'aff_sub1' => $user->unique_id,
            'firstname' => $user->first_name,
            'lastname' => $user->last_name,
            'middlename' => $user->middlename,
            'birthdate' => $user->birthdate,
            'email' => $user->getRealEmail(),
            'mphone' => Str::replace('+', '', $user->mphone),
            'agree_accepted' => '1',
            'agree_date' => $user->created_at->toDateTimeString(),
            'fact_region_name' => $user->geo_region,
            'credit_sum' => 20000,
        ];

        $response = Http::get($baseUrl, $data)->json();

        Log::debug('Отправили информацию о пользователе в Leads', [
            'user_id' => $user->id,
            'request' => json_encode($data),
            'response' => $response,
        ]);

        if (Arr::get($response, 'status') === 'success') {
            return;
        }

        $errors = Arr::get($response, 'error.params');

        foreach ($errors as $error) {
            $errorMessage = Arr::get($error, 'message');

            if (Str::contains($errorMessage, ['уже существует', 'уже есть в системе'])) {
                throw new UserDuplicateException($errorMessage);
            }

            throw new UserNotEligibleException($errorMessage);
        }

        $jsonResponse = json_encode($response);

        throw new UserSendException($jsonResponse);
    }

    /**
     * Получить идентификатор оффера.
     * @return string
     */
    abstract protected function getOfferId(): string;
}