<?php

declare(strict_types=1);

namespace Tradehub\Adapters;

class TradeHubGetSellerTokenPartner extends TradeHubPayloadAdapter
{
    /** @var string|null */
    private $token;

    public function __construct(?string $token)
    {
        $this->token = $token;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return null[]|string[]
     */
    public function jsonSerialize(): array
    {
        return [
            'token' => $this->getToken()
        ];
    }
}
