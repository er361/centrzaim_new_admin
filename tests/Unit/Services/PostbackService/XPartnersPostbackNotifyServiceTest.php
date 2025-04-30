<?php

namespace Tests\Unit\Services\PostbackService;

use App\Models\User;
use App\Services\PostbackService\Providers\XPartnersPostbackNotifyService;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class XPartnersPostbackNotifyServiceTest extends TestCase
{
    /**
     * Тестируем успешную отправку постбэка.
     * @return void
     */
    public function test(): void
    {
        Http::fake([
            'offers-xpartners.affise.com/*' => Http::response('ok'),
        ]);


        setting([
            SettingNameEnum::XPartnersToken->value => Str::random(),
            SettingNameEnum::XPartnersCustomField->value => Str::random(),
        ])->save();
        $user = User::factory()->createOne();
        $notifyService = new XPartnersPostbackNotifyService();
        $notifyService->send($user);

        Http::assertSent(function (Request $request) use ($user) {
            $noQueryRequestUrl = Str::before($request->url(), '?');
            $isUrlSame = $noQueryRequestUrl == 'https://offers-xpartners.affise.com/postback';
            $params = [
                'clickid' => $user->transaction_id,
                'action_id' => $user->unique_id,
                'secure' => SettingsService::getByKey(SettingNameEnum::XPartnersToken),
                'status' => config('services.x_partners.statuses.approved'),
                'custom_field1' => SettingsService::getByKey(SettingNameEnum::XPartnersCustomField),
            ];

            $requestData = $request->data();
            $isFieldsCorrect = true;

            foreach ($params as $key => $value) {
                $isFieldsCorrect = $isFieldsCorrect && $requestData[$key] === (string)$value;
            }

            return $isUrlSame && $isFieldsCorrect;
        });
    }
}