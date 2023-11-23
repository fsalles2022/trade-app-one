<?php

declare(strict_types=1);

namespace TimBR\Connection\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

class MercadoMoveisTim extends TimBRBaseHeader implements TimHeader
{
    protected $network = NetworkEnum::MERCADO_MOVEIS;

    /** @return string[] */
    public function credentials(): array
    {
        return [$this->getClientId(), $this->getClientSecret()];
    }

    public function getClientId(): string
    {
        return config('integrations.timBR.mercado-moveis.client-id');
    }

    public function getClientSecret(): string
    {
        return config('integrations.timBR.mercado-moveis.client-secret');
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
        return config('integrations.timBR.mercado-moveis.redirect-uri');
    }
}
