<?php

namespace ClaroBR\Components;

class ClaroBRDomains
{
    public static function formatPointOfSaleIdentifier($identifier): string
    {
        if (self::validatePointOfSaleIdentifier($identifier)) {
            return strtoupper($identifier);
        }

        throw new  \InvalidArgumentException(trans('siv::exceptions.InvalidClaroCode.message'));
    }

    public static function validatePointOfSaleIdentifier(string $identifier): bool
    {
        $strLengh          =  strlen($identifier);
        $specialCharacters = preg_match('/^[A-Za-z0-9@#%&*]*(?:-[A-Za-z0-9]+)*$/', $identifier);

        if ($strLengh >= 4 && $specialCharacters) {
            return true;
        }

        return false;
    }
}
