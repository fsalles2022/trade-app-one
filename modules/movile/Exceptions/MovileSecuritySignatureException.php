<?php

namespace Movile\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class MovileSecuritySignatureException extends ThirdPartyExceptions
{
    public function __construct()
    {
        $this->message = trans('movile::messages.security.signature.hash_error');
    }

    public function getShortMessage()
    {
        return 'SecuritySignatureException';
    }

    public function getDescription()
    {
        return trans('movile::exceptions.security.signature.hash_error');
    }
}
