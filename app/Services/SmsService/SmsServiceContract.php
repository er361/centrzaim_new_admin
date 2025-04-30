<?php


namespace App\Services\SmsService;


use App\Models\SmsProvider;

interface SmsServiceContract
{
    /**
     * Отправить SMS пользователю.
     * @param SmsProvider $smsProvider
     * @param string $phone
     * @param string $message
     * @return null|string Внешний идентификатор SMS или null, если не поддерживается
     */
    public function send(SmsProvider $smsProvider, string $phone, string $message, string $from = null): ?string;

    /**
     * Проверить статус отправки SMS.
     * @param SmsProvider $smsProvider
     * @param string[] $apiIds Список внешних идентификаторов SMS
     * @return int[] Список статусов, где ключ - внешний идентификатор SMS, а значение - статус
     */
    public function checkStatus(SmsProvider $smsProvider, array $apiIds): array;
}