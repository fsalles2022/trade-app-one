<?php

namespace NextelBR\Exceptions;

use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Exceptions\ThirdPartyExceptions;

class EligibilityException extends ThirdPartyExceptions
{
    public function __construct(Responseable $message)
    {
        $arrayResponse = $message->toArray();
        $this->message = data_get($arrayResponse, 'mensagem');
    }

    public function getShortMessage()
    {
        return 'ElegibilityException';
    }

    public function getDescription()
    {
        return '';
    }
}
