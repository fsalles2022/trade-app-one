<?php

namespace OiBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class OiBRUnavailable extends ThirdPartyExceptions
{
    public function __construct(string $message = "")
    {
        $this->transportedMessage = $message;
    }

    public function getShortMessage()
    {
        return 'OiBRUnavailable';
    }

    public function getDescription()
    {
        return trans('exceptions.third_party_unavailable', ['service' => 'Oi']);
    }
}
