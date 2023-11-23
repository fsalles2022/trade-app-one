<?php

namespace ClaroBR\Tests\Unit\Fixtures;

use ClaroBR\Enumerators\SivOperations;

class SivSalesFixture
{
    public static function oneSale(string $pdvCNPJ, string $serviceType, string $source = 'WEB', string $operation = SivOperations::POS_PAGO)
    {
        $sale      = file_get_contents(__DIR__ . '/SaleFromClaro.json');
        $sale      = str_replace('$source', $source, $sale);
        $sale      = str_replace('$pdvCNPJ', $pdvCNPJ, $sale);
        $sale      = str_replace('$operation', $operation, $sale);
        $sale      = str_replace('$serviceType', $serviceType, $sale);
        $converted = json_decode($sale, true);
        return $converted;
    }

    public static function oneSaleWithDependents(string $pdvCNPJ, string $serviceType, string $source = 'WEB', string $operation = SivOperations::POS_PAGO)
    {
        $sale      = file_get_contents(__DIR__ . '/SaleFromClaroWithDependent.json');
        $sale      = str_replace('$source', $source, $sale);
        $sale      = str_replace('$pdvCNPJ', $pdvCNPJ, $sale);
        $sale      = str_replace('$operation', $operation, $sale);
        $sale      = str_replace('$serviceType', $serviceType, $sale);
        $converted = json_decode($sale, true);
        return $converted;
    }

    public static function onePreSale(
        string $pdvCNPJ,
        string $serviceType,
        string $source = 'WEB',
        string $operation = SivOperations::POS_PAGO,
        bool $isPreSale = false
    ) {
        $sale      = file_get_contents(__DIR__ . '/PreSaleFromClaro.json');
        $sale      = str_replace('$source', $source, $sale);
        $sale      = str_replace('$isPreSale', $isPreSale, $sale);
        $sale      = str_replace('$pdvCNPJ', $pdvCNPJ, $sale);
        $sale      = str_replace('$operation', $operation, $sale);
        $sale      = str_replace('$serviceType', $serviceType, $sale);
        $converted = json_decode($sale, true);
        return $converted;
    }
}
