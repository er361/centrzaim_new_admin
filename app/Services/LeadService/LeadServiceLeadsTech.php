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
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class LeadServiceLeadsTech implements LeadServiceContract
{
    /**
     * Значение, на которое умножаем идентификатор сайта.
     */
    protected const SITE_ID_MULTIPLIER = 100000;

    /**
     * Токен для запросов.
     * @var string
     */
    protected string $token;

    /**
     * Идентификатор сайта.
     * @var int
     */
    protected int $siteId;

    /**
     * Название сайта.
     * @var string
     */
    protected string $siteName;

    /**
     * @var GeoService
     */
    protected GeoService $geoService;

    /**
     * LeadServiceLeadsTech constructor.
     */
    public function __construct()
    {
        $this->token = SettingsService::getByKey(SettingNameEnum::LeadsTechToken);
        $this->siteId = config('postbacks.site_id');
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

        $baseUrl = 'https://api.gate.leadfinances.com/v1/lead/add';

        $channelId = $this->siteId * self::SITE_ID_MULTIPLIER;
        $channelName = $this->siteName . ' / Нет вебмастера';

        $data = [
            'token' => $this->token,
            'phone' => $user->mphone,
            'type' => 1, // Займ
            'policy_accept' => 1,
            'mailings_accept' => 1,

            'channel_id' => $channelId,
            'channel_name' => $channelName,

            'sub_id1' => $user->unique_id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'middle_name' => $user->middlename,
            'birthday' => $user->birthdate,
            'email' => $user->getRealEmail(),

            'ip' => $user->ip_address,
            'source' => URL::to('/'),

            'region_fact' => $user->geo_region,
            'city_fact' => $user->geo_city,
            'amount' => 20000,
        ];

        $response = Http::get($baseUrl, $data)->json();

        Log::debug('Отправили информацию о пользователе в LeadsTech', [
            'user_id' => $user->id,
            'request' => json_encode($data),
            'response' => $response,
        ]);

        if (Arr::get($response, 'status') === 'OK') {
            return;
        }

        $errorMessage = Arr::get($response, 'message', '');

        if (Str::contains($errorMessage, 'duplicate')) {
            throw new UserDuplicateException($errorMessage);
        }

        if (Str::contains($errorMessage, 'Lead don\'t save')) {
            throw new UserNotEligibleException($errorMessage);
        }

        if ($this->isJson($errorMessage)) {
            $errorObject = json_decode($errorMessage);

            foreach ($errorObject as $field => $fieldErrors) {
                foreach ($fieldErrors as $fieldError) {
                    throw new UserNotEligibleException($fieldError);
                }
            }
        }

        throw new UserSendException($errorMessage);
    }

    /**
     * Проверка, является ли строка JSON.
     * @param string $json
     * @return bool
     */
    protected function isJson(string $json): bool
    {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }
}