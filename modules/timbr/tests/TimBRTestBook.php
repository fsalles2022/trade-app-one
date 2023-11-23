<?php

namespace TimBR\Tests;

final class TimBRTestBook
{
    const SUCCESS_USER                   = '54498269829';
    const SUCCESS_USER_NETWORK           = 'cea';
    const SUCCESS_CUSTOMER               = '00000009652';
    const SUCCESS_ZIPCODE                = '06454000';
    const SUCCESS_CUSTOMER_EXPRESS       = '40305666002';
    const SUCCESS_CUSTOMER_FLEX          = '26584329097';
    const SUCCESS_CUSTOMER_EXPRESS_CC    = '4242424242424242';
    const SUCCESS_CUSTOMER_BLACK         = '96058669006';
    const SUCCESS_CUSTOMER_BLACK_EXPRESS = '82095738020';
    const SUCCESS_CUSTOMER_BLACK_MULTI   = '67586346046';


    const FAILURE_CUSTOMER_ELIGIBILITY = '08854004022';
    const FAILURE_CUSTOMER_EXPRESS     = '53381544047';

    const EXPRESS_PRODUCTS = [ '1-5FOY6', '1-3QHE7'];
    const FATURA_PRODUCTS  = [ '1-118LLUU', '1-IL65OW'];

    public static function getCeaBearer()
    {
         return file_get_contents(base_path() . '/modules/timbr/tests/ServerTest/ceaBearer.json');
    }
}
