<?php

namespace NextelBR\Adapters\Request;

use NextelBR\Enumerators\NextelBRFormats;
use TradeAppOne\Domain\Adapters\RequestAdapterBehavior;
use TradeAppOne\Domain\Components\Helpers\DateConvertHelper;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Models\Collections\Service;

class AdhesionRequestAdapter implements RequestAdapterBehavior
{
    public static function adapt(Service $service, $extra = null)
    {
        $msisdnSanitized = null;
        if ($service->mode == Modes::PORTABILITY) {
            $msisdn          = data_get($service, 'portedNumber');
            $msisdnSanitized = MsisdnHelper::removeCountryCode(CountryAbbreviation::BR, $msisdn);
        }
        $portabilityDate = data_get($service, 'portability.portabilityDate');
        $portabilityDate = DateConvertHelper::convertToStringFormat($portabilityDate, NextelBRFormats::DATES);

        return array_filter([
            "dataPortabilidade"        => $portabilityDate,
            "faturaNoEmail"            => true,
            "idOperadoraPortabilidade" => data_get($service, 'portability.fromOperatorId'),
            "msisdnPortabilidade"      => $msisdnSanitized,
            "operadoraPortabilidade"   => data_get($service, 'portability.fromOperator'),
            "optOut"                   => true,
            "imeiAparelhoAdquirido"    => $service->imei ?? ''
        ]);
    }
}
