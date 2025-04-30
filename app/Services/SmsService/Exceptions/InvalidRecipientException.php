<?php

namespace App\Services\SmsService\Exceptions;

use RuntimeException;

/**
 * По данному номеру не может быть отправлено SMS сообщение.
 */
class InvalidRecipientException extends RuntimeException
{
    // Nothing
}