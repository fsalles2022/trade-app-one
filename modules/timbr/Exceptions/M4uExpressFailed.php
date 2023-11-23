<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class M4uExpressFailed extends ThirdPartyExceptions
{
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getShortMessage()
    {
        return 'M4uExpressFailed';
    }

    public function getDescription()
    {
        // TODO: Implement getDescription() method.
    }
}
