<?php

namespace App\Services\LeadService;

use App\Models\User;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LeadServiceDigitalContact implements LeadServiceContract
{
    /**
     * @var string
     */
    protected string $postbacksAdbSubPrefix;

    /**
     * @var string Токен для нового API
     */
    protected string $apiToken;

    /**
     * LeadServiceLeads constructor.
     */
    public function __construct()
    {
        $this->postbacksAdbSubPrefix = Str::replaceLast('_', '', config('postbacks.adv_sub_prefix'));
        $this->apiToken = SettingsService::getByKey(SettingNameEnum::DigitalContactApiKey);
    }

    /**
     * Отправляет информацию о пользователе.
     * @param User $user
     * @return void
     * @throws \Throwable
     */
    public function send(User $user): void
    {
        $data = [
            'list_id' => '2', // константа
            'email' => $user->getRealEmail(),
            'first_name' => $user->first_name,
            'company' => 'Zaimsrichno.su', // константа
            'is_confirmed' => '1',
            'is_active' => '1',
            'is_unsubscribed' => '0',
            'last_name' => $user->last_name,
            'Source' => $this->postbacksAdbSubPrefix,
            'Campaign' => $user->webmaster_id ?? '0',
            'Feed' => $user->webmaster?->source->name ?? 'direct',
            'response' => '1',
            'api_token' => $this->apiToken,
        ];

        $data = array_filter($data);
        $url = 'https://esp.intellemail.net/api/addContact';

        $response = Http::acceptJson()
            ->post($url, $data)
            ->json();

        Log::debug('Отправили информацию о пользователе в DigitalContact (новое API)', [
            'user_id' => $user->id,
            'request' => json_encode($data),
            'response' => $response,
        ]);
    }
}