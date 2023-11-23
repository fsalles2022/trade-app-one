<?php

namespace TradeAppOne\Domain\Components\Traits;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Exceptions\BusinessExceptions\InvalidServiceStatus;

trait ServiceHelper
{
    public function setStatus(string $status)
    {
        if (! array_search($status, self::STATUS)) {
            throw new InvalidServiceStatus($status);
        }
        $this->attributes['attributes'] = $status;
    }

    public function needToBeIntegreted(): bool
    {
        return is_null($this->operatorIdentifiers);
    }

    public function isNotApproved(): bool
    {
        return ! $this->isApproved();
    }

    public function operationIs($operations): bool
    {
        return in_array($this->operation, $operations);
    }

    public function isCanceled(): bool
    {
        return $this->status == ServiceStatus::CANCELED;
    }

    public function isAccepted(): bool
    {
        return $this->status == ServiceStatus::ACCEPTED;
    }

    public function isApproved(): bool
    {
        return $this->status == ServiceStatus::APPROVED;
    }

    public function isTradeIn(): bool
    {
        return $this->operator === Operations::TRADE_IN_MOBILE;
    }

    public function isTriangulation(): bool
    {
        return data_get($this->attributes, 'discount.id') && $this->sector === Operations::TELECOMMUNICATION;
    }

    public function isNotActivated(): bool
    {
        return ! in_array($this->status, [ServiceStatus::ACCEPTED, ServiceStatus::APPROVED], true);
    }

    public function priceInCents(): string
    {
        return bcmul($this->price, 100, 0);
    }

    public function getTokenCard(): ?string
    {
        return data_get($this->attributes, 'card.token');
    }

    public function getGatewayTransaction(): ?string
    {
        return data_get($this->attributes, 'payment.gatewayTransactionId');
    }

    public function getPaymentStatus(): ?string
    {
        return data_get($this->attributes, 'payment.status');
    }
}
