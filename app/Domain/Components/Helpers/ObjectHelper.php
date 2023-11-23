<?php

namespace TradeAppOne\Domain\Components\Helpers;

class ObjectHelper
{
    public static function convertToArray($object): array
    {
        return array_wrap(json_decode(json_encode($object), true));
    }

    public static function convertToJson($object): string
    {
        return json_encode($object, true);
    }
}
