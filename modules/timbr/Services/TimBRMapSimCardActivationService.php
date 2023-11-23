<?php

declare(strict_types=1);

namespace TimBR\Services;

use TimBR\Enumerators\TimBRSegments;
use TimBR\Models\Eligibility;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

class TimBRMapSimCardActivationService
{
    /**
     * @param mixed[] $service
     * @return mixed[]
     */
    public static function map(array $service): array
    {
        $areaCode     = $service['areaCode'] ?? null;
        $portedNumber = $service['portedNumber'] ?? null;

        return [
            'customer' => [
                'type' => 'POSTPAID',
                'area' => empty($areaCode) ? mb_substr($portedNumber, 0, 2) : $areaCode,
            ],
            'device' => [
                'iccid' => $service['iccid'] ?? null,
            ]
        ];
    }
}
