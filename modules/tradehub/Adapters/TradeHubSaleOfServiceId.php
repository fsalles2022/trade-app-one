<?php

declare(strict_types=1);

namespace Tradehub\Adapters;

class TradeHubSaleOfServiceId extends TradeHubPayloadAdapter
{
    /** @var string */
    private $saleOfServiceId;

    public function __construct(string $saleOfServiceId)
    {
        $this->saleOfServiceId = $saleOfServiceId;
    }

    public function getSaleOfServiceId(): string
    {
        return $this->saleOfServiceId;
    }

    /** @return string[] */
    public function jsonSerialize(): array
    {
        return [
            'saleOfServiceId' => $this->getSaleOfServiceId(),
        ];
    }
}
