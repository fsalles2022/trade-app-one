<?php

namespace TimBR\Connection\Headers;

use Illuminate\Support\Facades\Cache;
use TimBR\Enumerators\TimBRCacheables;
use TimBR\Exceptions\TimBRBearerSergeantFailed;
use TimBR\Exceptions\TimBRSergeantNetworkNotFound;

class TimBRBaseHeader
{
    protected $network;

    public function getSergeant(string $cpf): string
    {
        $timUserBearerNetworkKey = TimBRCacheables::USER_BEARER . $this->getNetwork() . $cpf;
        $bearer                  = Cache::get($timUserBearerNetworkKey);

        if ($bearer) {
            return $this->extractSergeant($bearer);
        }

        throw new TimBRSergeantNetworkNotFound();
    }

    public function extractSergeant($bearer)
    {
        try {
            $base64body    = explode('.', $bearer)[1];
            $base64Decoded = base64_decode($base64body);
            return json_decode($base64Decoded)->lastname;
        } catch (\ErrorException $exception) {
            throw new TimBRBearerSergeantFailed();
        }
    }

    protected function getNetwork() : string
    {
        return $this->network;
    }
}
