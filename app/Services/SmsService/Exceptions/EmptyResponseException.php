<?php

namespace App\Services\SmsService\Exceptions;

use RuntimeException;
use Throwable;

class EmptyResponseException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Сервис вернул пустой ответ.');
    }
}