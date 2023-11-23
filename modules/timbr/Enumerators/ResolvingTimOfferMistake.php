<?php

namespace TimBR\Enumerators;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\Operations;

class ResolvingTimOfferMistake
{
    const DDDS_WITH_PRE       = ['27', '28', '38', '54', '68', '69', '94', '99'];
    const FIXED_OFFER         = 'PE';
    const FIXED_OFFER_DEFAULT = 'PR00430';

    public static function removingPlansWhereDDDisNotIn(Collection $plans, $payload): Collection
    {
        $operation = data_get($payload, 'operation');
        $areaCode  = data_get($payload, 'areaCode');

        if (empty($areaCode)) {
            $areaCode = MsisdnHelper::getAreaCode(data_get($payload, 'portedNumber', ''));
        }
        if ($operation == Operations::TIM_PRE_PAGO && in_array($areaCode, self::DDDS_WITH_PRE)) {
            return collect([]);
        }
        return $plans;
    }
}
