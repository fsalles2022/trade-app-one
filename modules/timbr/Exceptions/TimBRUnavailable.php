<?php

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBRUnavailable extends ThirdPartyExceptions
{
    public function getShortMessage()
    {
        return 'TimBRUnavailable';
    }

    public function getDescription()
    {
        return trans('exceptions.third_party_unavailable', ['service' => 'TIM']);
    }
}
