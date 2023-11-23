<?php

namespace McAfee\Enumerators;

class McAfeeSKU
{
    public const MOBILE_SECURITY = '1343-93222-mmsu'; //Lifetime Access
    public const MULTI_ACCESS    = '1343-93224-1usermds'; //Lifetime Access

    public const MMA_YEARLY_1_DEVICE = '1343-123947-1usermds';
    public const MMA_YEARLY_3_DEVICE = '1343-123996-3dumma';
    public const MMA_YEARLY_5_DEVICE = '1343-123994-5dmma';

    public const REQUIRED_NUMBER_PHONE = [
        self::MOBILE_SECURITY
    ];

    public static function requireNumberPhone(string $sku): bool
    {
        return in_array($sku, self::REQUIRED_NUMBER_PHONE, true);
    }
}
