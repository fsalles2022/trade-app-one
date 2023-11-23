<?php

namespace VivoBR\Tests\Fixtures;

class SalesFromSun
{
    const TOTAL_NOT_API_SERVICES = 315;
    const TOTAL_SERVICES         = 315;
    public static function allSalesFromNetworks()
    {
        $sales     = file_get_contents(__DIR__ . '/salesFromVivo.json');
        $converted = json_decode($sales, true);
        return $converted;
    }
}
