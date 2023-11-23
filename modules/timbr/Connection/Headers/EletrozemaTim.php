<?php


namespace TimBR\Connection\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

class EletrozemaTim extends TimBRBaseHeader implements TimHeader
{
    protected $network = NetworkEnum::ELETROZEMA;

    public function credentials(): array
    {
        return [$this->getClientId(), $this->getClientSecret()];
    }

    public function getClientId(): string
    {
        return config('integrations.timBR.eletrozema.client-id');
    }

    public function getClientSecret(): string
    {
        return config('integrations.timBR.eletrozema.client-secret');
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
        return config('integrations.timBR.eletrozema.redirect-uri');
    }
}
