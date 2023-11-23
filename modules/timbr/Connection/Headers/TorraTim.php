<?php

declare(strict_types=1);

namespace TimBR\Connection\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

class TorraTim extends TimBRBaseHeader implements TimHeader
{
    protected $network = NetworkEnum::LOJAS_TORRA;

    /** @return string[] */
    public function credentials(): array
    {
        return [$this->getClientId(), $this->getClientSecret()];
    }

    public function getClientId(): string
    {
        return config('integrations.timBR.lojas-torra.client-id');
    }

    public function getClientSecret(): string
    {
        return config('integrations.timBR.lojas-torra.client-secret');
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
        return config('integrations.timBR.lojas-torra.redirect-uri');
    }
}
