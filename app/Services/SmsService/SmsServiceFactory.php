<?php


namespace App\Services\SmsService;


use App\Models\SmsProvider;
use App\Services\SmsService\SmsRu\SmsRuSender;
use UnexpectedValueException;

class SmsServiceFactory
{
    /**
     * @param int $serviceId
     * @return SmsServiceContract
     */
    public function getService(int $serviceId): SmsServiceContract
    {
        if ($serviceId === SmsProvider::SERVICE_MY_SMPP) {
            return new MySmppService();
        }

        if ($serviceId === SmsProvider::SERVICE_SMS_RU) {
            return new SmsRuSender();
        }

        throw new UnexpectedValueException("Service with ID {$serviceId} does not exists.");
    }
}