<?php

namespace TradeAppOne\Domain\Adapters;

use TradeAppOne\Domain\Models\Collections\Service;

interface RequestAdapterBehavior
{
    public static function adapt(Service $service, $extra = null);
}
