<?php

namespace TradeAppOne\Domain\Components\Helpers;

class XMLHelper
{
    public static function convertToArray(string $xml): array
    {
        $adapter = simplexml_load_string($xml, null, LIBXML_NOCDATA);
        return json_decode(json_encode($adapter), true);
    }
}
