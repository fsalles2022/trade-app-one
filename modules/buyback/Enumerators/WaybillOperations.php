<?php

namespace Buyback\Enumerators;

use TradeAppOne\Domain\Enumerators\Operations;

final class WaybillOperations
{
    public const AVAILABLES = [
        Operations::SALDAO_INFORMATICA,
        Operations::TRADE_NET,
        Operations::IPLACE_ANDROID,
        Operations::IPLACE_IPAD,
        Operations::IPLACE,
        Operations::TRADE_UP,
        Operations::WATCH
    ];
}
