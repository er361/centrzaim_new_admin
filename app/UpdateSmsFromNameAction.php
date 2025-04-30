<?php

namespace App;

use App\Models\SmsProvider;
use App\Services\SmsService\SmsRu\ParseAlphaNamesAction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateSmsFromNameAction
{
    public function __construct()
    {
    }

    public function run()
    {
        SmsProvider::each(function (SmsProvider $provider) {
            $name = $this->getName($provider);
            $provider->update(['from_name' => $name]);
        });
    }

    private function getName(SmsProvider $provider):array
    {
        return match ($provider->service_id) {
            SmsProvider::SERVICE_SMS_RU => (new ParseAlphaNamesAction())
                ->run($provider->api_login),
            default => []
        };
    }
}
