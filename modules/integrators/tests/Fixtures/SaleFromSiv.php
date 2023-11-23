<?php


namespace Integrators\tests\Fixtures;

class SaleFromSiv
{
    public static function residentialSale()
    {
        $saleJson = file_get_contents(__DIR__ . '/sale_from_siv.json');
        return json_decode($saleJson, true);
    }
}
