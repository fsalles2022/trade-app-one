<?php

namespace McAfee\Enumerators;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\NetworkEnum;

class McAfeePlansNetworks
{
    public static function filter(): Collection
    {
        return McAfeePlans::get();
    }
}
