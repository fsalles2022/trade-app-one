<?php

declare(strict_types=1);

namespace TimBR\Services;

use TimBR\Enumerators\TimBRSegments;
use TimBR\Models\Eligibility;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

class TimBRMapCheckMasterMsisdnService
{
    /**
     * @param mixed[] $payload
     * @return mixed[]
     */
    public static function map(array $payload): array
    {
        return [
            'type' => 'M',
            'socialSecNo' => data_get($payload, 'customer.cpf'),
        ];
    }
}
