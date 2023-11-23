<?php

declare(strict_types=1);

namespace TimBR\Connection\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

class SamsungTim extends TimBRBaseHeader implements TimHeader
{
    protected $network = NetworkEnum::SAMSUNG;

    public function credentials(): array
    {
        return [$this->getClientId(), $this->getClientSecret()];
    }

    public function getClientId(): string
    {
        return config('integrations.timBR.samsung.client-id');
    }

    public function getClientSecret(): string
    {
        return config('integrations.timBR.samsung.client-secret');
    }

    public function getBasicAuth(): string
    {
        return 'Basic ' . base64_encode($this->getClientId() . ':' . $this->getClientSecret());
    }

    public function getRedirectUriEncoded(): string
    {
        return urlencode($this->getRedirectUri());
    }

    public function getRedirectUri(): string
    {
        return config('integrations.timBR.samsung.redirect-uri');
    }
}
