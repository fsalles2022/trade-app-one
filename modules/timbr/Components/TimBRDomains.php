<?php

namespace TimBR\Components;

class TimBRDomains
{
    public static function validatePointOfSaleIdentifier(string $identifier): bool
    {
        return preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $identifier);
    }

    public static function formatPointOfSaleIdentifier($identifier): string
    {
        if (self::validatePointOfSaleIdentifier($identifier)) {
            return strtoupper($identifier);
        }

        throw new  \InvalidArgumentException(trans('timBR::exceptions.InvalidTimCode.message'));
    }
}
