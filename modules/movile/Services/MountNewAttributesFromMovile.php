<?php

namespace Movile\Services;

use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Services\MountNewAttributesService;

class MountNewAttributesFromMovile implements MountNewAttributesService
{
    public function getAttributes(array $service): array
    {
        try {
            $msisdnWithoutCountryCode = data_get($service, 'msisdn');
            $msisdn                   = MsisdnHelper::addCountryCode(CountryAbbreviation::BR, $msisdnWithoutCountryCode);
            if (app()->environment() == Environments::PRODUCTION) {
                $product = 'com.movile.cubes.cea.semester';
            } else {
                $product = 'com.movile.cubes.cea.semester.homolog';
            }
            return compact('msisdn', 'product');
        } catch (\ErrorException $exception) {
            return [];
        }
    }
}
