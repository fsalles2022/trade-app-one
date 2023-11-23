<?php

declare(strict_types=1);

namespace McAfee\Adapters\Request;

use Carbon\Carbon;
use TradeAppOne\Domain\Models\Collections\Service;

class McAfeeBaseRequest
{
    protected static function getCustomerIdByService(Service $service): string
    {
        $cutDateFromNewFormat = new Carbon('2022-07-05 00:00:00');

        // Sales made before 2022-07-05 00:00:00 should use old customer format
        if ($service->sale->createdAt->lt($cutDateFromNewFormat)) {
            return $service->customer['cpf'];
        }

        return self::getNewCustomerFormatWithCustomerCpfAndSaleCreatedAtByService($service);
    }

    private static function getNewCustomerFormatWithCustomerCpfAndSaleCreatedAtByService(Service $service): string
    {
        return "{$service->customer['cpf']}_{$service->sale->createdAt->format('YmdHi')}";
    }
}
