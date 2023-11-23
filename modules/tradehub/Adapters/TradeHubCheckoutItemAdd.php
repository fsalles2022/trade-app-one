<?php

declare(strict_types=1);

namespace Tradehub\Adapters;

class TradeHubCheckoutItemAdd extends TradeHubPayloadAdapter
{
    /** @var string */
    private $checkoutItemId;

    /** @var string */
    private $saleOfServiceId;

    /** @var string */
    private $productId;

    /** @var string */
    private $promotionId;

    /** @var string|null */
    private $productResidentialProductTypeId;

    /** @var int */
    private $productResidentialProductAmount;

    public function __construct(
        ?string $checkoutItemId,
        string $saleOfServiceId,
        string $productId,
        string $promotionId,
        ?string $productResidentialProductTypeId = null,
        int $productResidentialProductAmount = 0
    ) {
        $this->checkoutItemId                  = $checkoutItemId;
        $this->saleOfServiceId                 = $saleOfServiceId;
        $this->productId                       = $productId;
        $this->promotionId                     = $promotionId;
        $this->productResidentialProductTypeId = $productResidentialProductTypeId;
        $this->productResidentialProductAmount = $productResidentialProductAmount;
    }

    public function getCheckoutItemId(): ?string
    {
        return $this->checkoutItemId;
    }

    public function getSaleOfServiceId(): string
    {
        return $this->saleOfServiceId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getPromotionId(): string
    {
        return $this->promotionId;
    }

    public function getProductResidentialProductTypeId(): ?string
    {
        return $this->productResidentialProductTypeId;
    }

    public function getProductResidentialProductAmount(): int
    {
        return $this->productResidentialProductAmount;
    }

    /** @return string[] */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getCheckoutItemId(),
            'saleOfServiceId' => $this->getSaleOfServiceId(),
            'productId' => $this->getProductId(),
            'productPromotionId' => $this->getPromotionId(),
            'productResidentialPointAdditionalTypeId' => $this->getProductResidentialProductTypeId(),
            'productResidentialPointAdditionalAmount' => $this->getProductResidentialProductAmount(),
        ];
    }
}
