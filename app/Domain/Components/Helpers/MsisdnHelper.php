<?php

namespace TradeAppOne\Domain\Components\Helpers;

use TradeAppOne\Domain\Enumerators\BrasilAreaCodes;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;

class MsisdnHelper
{
    const BR                           = 'BR';
    const MIN_LENGTH                   = 11;
    const DIAL_CODES                   = [
        CountryAbbreviation::BR => '+55'
    ];
    const CODES                        = [
        CountryAbbreviation::BR => '55'
    ];
    const MIN_LENGTH_WITH_COUNTRY_CODE = [
        CountryAbbreviation::BR => 11
    ];

    const MIN_LENGTH_WITHOUT_COUNTRY_CODE = [
        CountryAbbreviation::BR => 10
    ];

    public static function getAreaCode(string $msisdn): ?string
    {
        $msisdn = self::removeCountryCode(CountryAbbreviation::BR, $msisdn);
        if (strlen($msisdn) >= BrasilAreaCodes::LENGTH_COUNTRY) {
            return substr($msisdn, 0, 2);
        }
        return null;
    }

    public static function removeCountryCode(string $country, $msisdn)
    {
        try {
            if (strlen($msisdn) > self::MIN_LENGTH_WITH_COUNTRY_CODE[$country]) {
                if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $msisdn)) {
                    return str_replace(self::DIAL_CODES[$country], '', $msisdn);
                } else {
                    return preg_replace('/^'.self::CODES[$country].'/', '', $msisdn);
                }
            } else {
                return $msisdn;
            }
        } catch (\ErrorException $exception) {
            return $msisdn;
        }
    }

    public static function removeSumSign(string $country, string $msisdn)
    {
        if (strlen($msisdn) >= self::MIN_LENGTH[$country] && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $msisdn)) {
            return str_replace('+', '', $msisdn);
        }
    }

    public static function addDialCountryCode(string $country, string $msisdn)
    {
        if (strlen($msisdn) >= self::MIN_LENGTH_WITHOUT_COUNTRY_CODE[$country]) {
            return self::DIAL_CODES[$country] . $msisdn;
        }
    }

    public static function addCountryCode(string $country, string $msisdn)
    {
        if (strlen($msisdn) >= self::MIN_LENGTH_WITHOUT_COUNTRY_CODE[$country]) {
            return self::CODES[$country] . $msisdn;
        }
    }
}
