<?php

namespace OiBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class OiBRElDoradoUnavailable extends ThirdPartyExceptions
{
    public function getShortMessage()
    {
        return 'OiBRElDoradoUnavailable';
    }

    public function getDescription()
    {
        return 'OiBRElDoradoUnavailable';
    }
}
