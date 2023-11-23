<?php

declare(strict_types=1);

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class BrScanAuthenticationException extends ThirdPartyExceptions
{
    public function getShortMessage(): string
    {
        return 'BrScanAuthenticationException';
    }

    public function getDescription(): string
    {
        return trans('exceptions.third_party_unavailable', ['service' => 'TIM_BRSCAN_AUTH']);
    }
}
