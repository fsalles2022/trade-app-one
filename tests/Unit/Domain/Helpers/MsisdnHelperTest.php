<?php

namespace TradeAppOne\Tests\Unit\Domain\Helpers;

use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Domain\Enumerators\CountryAbbreviation;
use TradeAppOne\Tests\TestCase;

class MsisdnHelperTest extends TestCase
{
    /** @test */
    public function should_return_national_number_without_country_code_when_phone_number_sent_without_country_code()
    {
        $number        = '2132313553';
        $nationalPhone = MsisdnHelper::removeCountryCode(CountryAbbreviation::BR, $number);
        self::assertEquals(10, strlen($nationalPhone));
        self::assertEquals($number, $nationalPhone);
    }

    /** @test */
    public function should_return_national_number_without_country_code_when_phone_number_sent()
    {
        $number        = '+552132313553';
        $nationalPhone = MsisdnHelper::removeCountryCode(CountryAbbreviation::BR, $number);
        self::assertEquals(10, strlen($nationalPhone));
        self::assertNotContains('+55', $nationalPhone);
    }


    /** @test */
    public function should_return_national_number_without_country_code_when_mobile_number_sent()
    {
        $number        = '+5521991919947';
        $nationalPhone = MsisdnHelper::removeCountryCode(CountryAbbreviation::BR, $number);
        self::assertEquals(11, strlen($nationalPhone));
        self::assertNotContains('+55', $nationalPhone);
    }

    /** @test */
    public function should_return_national_number_without_country_code_when_8_number_sent()
    {
        $number        = '+552191919947';
        $nationalPhone = MsisdnHelper::removeCountryCode(CountryAbbreviation::BR, $number);
        self::assertEquals(10, strlen($nationalPhone));
        self::assertNotContains('+55', $nationalPhone);
    }

    /** @test */
    public function return_area_when_msisdn_have_not_country_code()
    {
        $number   = '55991919947';
        $areaCode = MsisdnHelper::getAreaCode($number);

        self::assertEquals('55', $areaCode);
    }

    /** @test */
    public function return_msisdn_country_code()
    {
        $number = '55991919947';
        $msisdn = MsisdnHelper::addCountryCode(CountryAbbreviation::BR, $number);

        self::assertEquals(13, strlen($msisdn));
    }


    /** @test */
    public function return_without_sum()
    {
        $number   = '+55991919947';
        $areaCode = MsisdnHelper::removeSumSign(MsisdnHelper::BR, $number);

        self::assertEquals('55991919947', $areaCode);
    }
}
