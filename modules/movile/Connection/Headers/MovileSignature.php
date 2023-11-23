<?php

namespace Movile\Connection\Headers;

use Movile\Connection\MovileRoutes;
use Movile\Exceptions\MovileSecuritySignatureException;

class MovileSignature
{
    public static function generate(string $body): ?string
    {
        if (self::isJSON($body)) {
            $secret = config('integrations.movile.secret');

            $path               = MovileRoutes::SUBSCRIBE;
            $verb               = "POST";
            $query              = "";
            $signatureParameter = "x-kiwi-signature";
            $hashedVerb         = hash_hmac("sha256", $verb, $secret, false);
            $hashedPath         = hash_hmac("sha256", $path, pack("H*", $hashedVerb), false);
            $hashedQuery        = hash_hmac("sha256", $query, pack("H*", $hashedPath), false);
            $hashedBody         = hash_hmac("sha256", $body, pack("H*", $hashedQuery), false);
            $rawSignature       = hash_hmac("sha256", $signatureParameter, pack("H*", $hashedBody), false);
            return base64_encode(pack("H*", $rawSignature));
        }

        throw new MovileSecuritySignatureException();
    }

    private static function isJSON(string $string): bool
    {
        return is_string($string) && is_array(json_decode($string, true));
    }
}
