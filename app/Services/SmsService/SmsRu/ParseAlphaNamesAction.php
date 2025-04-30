<?php

namespace App\Services\SmsService\SmsRu;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ParseAlphaNamesAction
{
    public function run(string $apiLogin): array
    {
        $logger = Log::channel('sms');
        $logger->debug('Обновление альфа имен');

        $query = [
            'api_id' => $apiLogin,
            'json' => '1',
        ];

        $url = config('sms.smsRu.url') . '/my/senders';

        try {
            $res = Http::get($url, $query)
                ->throw();

            $data = $res->json();

            if($data['status'] === 'OK' && $data['status_code'] === 100) {
                $logger->debug('Альфа имена получены');
                return $data['senders'];
            }

        } catch (\Throwable $exception) {
            $logger->error('Ошибка при обновлении имени', [
                'exception' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => \Illuminate\Support\Str::limit($exception->getTraceAsString(), 100),
            ]);
            return [];
        }

        $logger->debug('Альфа имена обновлены');

        return [];
    }
}
