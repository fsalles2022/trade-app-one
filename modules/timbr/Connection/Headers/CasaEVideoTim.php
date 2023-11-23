<?php


namespace TimBR\Connection\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

class CasaEVideoTim extends TimBRBaseHeader implements TimHeader
{
    protected $network = NetworkEnum::CASAEVIDEO;

    public function credentials(): array
    {
        return [$this->getClientId(), $this->getClientSecret()];
    }

    public function getClientId(): string
    {
        return config('integrations.timBR.casaevideo.client-id');
    }

    public function getClientSecret(): string
    {
        return config('integrations.timBR.casaevideo.client-secret');
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
        return config('integrations.timBR.casaevideo.redirect-uri');
    }
}
