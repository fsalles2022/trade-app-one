<?php

declare(strict_types=1);

namespace TimBR\Exceptions;

use TradeAppOne\Exceptions\ThirdPartyExceptions;

class TimBRCommissioningUnavailable extends ThirdPartyExceptions
{
    public function getShortMessage(): string
    {
        return 'TimBRCommissioningUnavailable';
    }

    public function getDescription(): string
    {
        return trans('exceptions.third_party_unavailable', ['service' => 'TIM_COMMISSIONING_SERVICE']);
    }
}
