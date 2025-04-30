<?php


namespace App\Services\SmsService\SmsRu;


use App\Models\SmsProvider;
use App\Services\SmsService\Exceptions\StatusCheckingNotSupportedException;
use App\Services\SmsService\SmsServiceContract;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsRuSender implements SmsServiceContract
{
    /**
     * Отправить SMS пользователю.
     *
     * @param SmsProvider $smsProvider
     * @param string $phone
     * @param string $message
     * @param string|null $from
     * @return null|string Внешний идентификатор SMS или null, если не поддерживается
     * @throws RequestException
     */
    public function send(SmsProvider $smsProvider, string $phone, string $message, string $from = null): ?string
    {
        $logger = Log::channel('sms');
        $logger->debug('Запрос к сервису SmsRu: ' . $phone);

        $query = $this->buildQuery($smsProvider, $phone, $message, $from);
        $url = config('sms.smsRu.url') . '/sms/send';

        try {
            Http::get($url, $query)
                ->throw();
        } catch (\Throwable $exception) {
            $logger->error('Ошибка при отправке SMS', [
                'query' => $query,
                'phone' => $phone,
                'message' => $message,
                'exception' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => \Illuminate\Support\Str::limit($exception->getTraceAsString(), 100),
            ]);
            return null;
        }

        $logger->debug('Отправлено SMS: ' . $phone . ' message: ' . $message);


        return null;
    }

    /**
     * Проверить статус отправки SMS.
     * @param SmsProvider $smsProvider
     * @param string[] $apiIds Список внешних идентификаторов SMS
     * @return int[] Список статусов, где ключ - внешний идентификатор SMS, а значение - статус
     */
    public function checkStatus(SmsProvider $smsProvider, array $apiIds): array
    {
        throw new StatusCheckingNotSupportedException();
    }

    /**
     * @param SmsProvider $smsProvider
     * @param string $phone
     * @param string $message
     * @param string|null $from
     * @return array
     */
    public function buildQuery(SmsProvider $smsProvider, string $phone, string $message, ?string $from): array
    {
        $query = [
            'api_id' => $smsProvider->api_login,
            'to' => $phone,
            'msg' => $message,
            'json' => '1',
        ];

        if ($from) {
            $query['from'] = $from;
        }
        return $query;
    }
}