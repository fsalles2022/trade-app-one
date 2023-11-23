<?php

namespace Buyback\Enumerators;

use TradeAppOne\Domain\Enumerators\Operations;

final class WaybillCarriers
{
    public const SPFLY   = 'SPFLY';
    public const WERTLOG = 'WERTLOG';

    public const CARRIER_BY_OPERATION = [
        Operations::SALDAO_INFORMATICA => self::WERTLOG,
        Operations::TRADE_NET => self::SPFLY
    ];
}
