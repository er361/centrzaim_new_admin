<?php

namespace App\Services\SmsService\Exceptions;

use RuntimeException;

/**
 * Ошибка означающая временные проблемы при отправке SMS.
 */
class SmsNotSentException extends RuntimeException
{
    // Nothing
}