<?php

namespace Outsourced\Riachuelo\Connections\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

class RiachueloHeaders
{
    const CONFIG = 'outsourced.'. NetworkEnum::RIACHUELO;

    public function getClientId(): string
    {
        return config(self::CONFIG . '.client_id');
    }

    public function getClientSecret(): string
    {
        return config(self::CONFIG . '.client_secret');
    }

    public function getUri(): string
    {
        return config(self::CONFIG . '.uri');
    }

    public function getBasicAuth(): string
    {
        return 'Basic ' . base64_encode($this->getClientId() . ':' . $this->getClientSecret());
    }
}
