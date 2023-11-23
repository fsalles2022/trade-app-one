<?php

declare(strict_types=1);

namespace Tradehub\Adapters;

class TradeHubCheckoutOrder extends TradeHubPayloadAdapter
{
    /** @var string */
    private $saleOfServiceId;

    /** @var array[] */
    private $products;

    public function __construct(string $saleOfServiceId, array $products)
    {
        $this->saleOfServiceId = $saleOfServiceId;
        $this->products        = $products;
    }

    public function getSaleOfServiceId(): string
    {
        return $this->saleOfServiceId;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    /** @return string[] */
    public function jsonSerialize(): array
    {
        return [
            'saleOfServiceId' => $this->getSaleOfServiceId(),
            'products' => $this->getProducts(),
        ];
    }
}
