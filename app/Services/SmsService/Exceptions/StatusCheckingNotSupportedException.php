<?php

namespace App\Services\SmsService\Exceptions;

use RuntimeException;
use Throwable;

class StatusCheckingNotSupportedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Status checking is not supported yet.');
    }
}