<?php

namespace VivoBR\Helpers;

use TradeAppOne\Domain\Components\Helpers\Blowfish\BlowfishHelper;

class SunHelper
{
    /** @throws */
    public static function crypt($plaintext, $key)
    {
        return base64_encode(
            BlowfishHelper::encrypt(
                $plaintext,
                $key,
                BlowfishHelper::BLOWFISH_MODE_EBC,
                BlowfishHelper::BLOWFISH_PADDING_ZERO
            )
        );
    }

    public static function cryptParams(array $params, $cryptKey)
    {
        return $params;
    }
}
