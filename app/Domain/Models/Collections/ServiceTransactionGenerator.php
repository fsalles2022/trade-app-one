<?php

namespace TradeAppOne\Domain\Models\Collections;

final class ServiceTransactionGenerator
{
    public static function generate(): string
    {
        $prefix          = date('YmdHis');
        $microtimeSuffix = substr(explode('.', explode(' ', microtime())[0])[1], 0, 4);
        return $prefix . $microtimeSuffix;
    }
}
