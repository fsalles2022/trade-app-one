<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBRElDoradoUnavailable extends ThirdPartyExceptions
{
    public function getShortMessage()
    {
        return 'TimBRElDoradoUnavailable';
    }

    public function getDescription()
    {
        return 'TimBRElDoradoUnavailable';
    }
}
