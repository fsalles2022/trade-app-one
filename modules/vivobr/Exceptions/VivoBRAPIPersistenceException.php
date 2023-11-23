<?php

namespace VivoBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class VivoBRAPIPersistenceException extends ThirdPartyExceptions
{
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getShortMessage()
    {
        return 'VivoBRAPIPersistenceException';
    }

    public function getDescription()
    {
        return '';
    }
}
