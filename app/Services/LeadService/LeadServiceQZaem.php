<?php

namespace App\Services\LeadService;

use App\Models\User;
use App\Services\LeadService\Exceptions\UserSendException;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class LeadServiceQZaem implements LeadServiceContract
{
    /**
     * @var string
     */
    protected string $utmSource;

    /**
     * @var string
     */
    protected string $apiKey;

    public function __construct()
    {
        $this->utmSource = SettingsService::getByKey(SettingNameEnum::QZaimUtmSource);
        $this->apiKey = SettingsService::getByKey(SettingNameEnum::QZaimApiKey);
    }

    /**
     * Отправляет информацию о пользователе.
     * @param User $user
     * @return void
     */
    public function send(User $user): void
    {
        try {
            $birthDate = Carbon::parse($user->birthdate);
        } catch (Throwable) {
            $birthDate = null;
        }

        $data = [
            'phone' => Str::replaceFirst('+7', '', $user->mphone),
            'firstname' => $user->first_name,
            'lastname' => $user->last_name,
            'middlename' => $user->middlename,
            'bday' => $birthDate?->toDateString(),
            'utm_source' => $this->utmSource,
            'utm_medium' => $user->webmaster?->source_id ? 'source_' . $user->webmaster->source_id : 'source_direct',
            'utm_campaign' => $user->webmaster?->id ? 'webmaster_' . $user->webmaster->id : null,
            'utm_term' => 'user_' . $user->id,
        ];

        $data = array_filter($data);
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        $goUrl = sprintf(
            'https://qzaem.ru/api_v1/add_user?api_key=%s&data=%s',
            $this->apiKey,
            $json,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $goUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $responseJson = json_decode($response, true);

        Log::debug('Отправили информацию о пользователе в QZaem', [
            'user_id' => $user->id,
            'request' => $json,
            'response' => $response,
        ]);

        $error = Arr::get($responseJson, 'error');

        if ($error === 'error api key') {
            throw new UserSendException($error);
        }
    }
}