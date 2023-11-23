<?php

declare(strict_types=1);

namespace TradeAppOne\Domain\Builder;

use TradeAppOne\Domain\Enumerators\Channels;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Http\Resources\PointOfSaleResource;

class OiResidentialSaleBuilder
{
    /** @var User */
    private $user;

    /** @var PointOfSale */
    private $pointOfSale;

    /** @var Service */
    private $service;

    /** @var mixed[] */
    private $customer;

    /** @var Sale */
    private $sale;

    public function buildUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /** @param mixed[] $customer */
    public function buildCustomer(array $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    public function buildPointOfSale(PointOfSale $pointOfSale): self
    {
        $this->pointOfSale = $pointOfSale;
        return $this;
    }

    /** @param mixed[] $attributesService */
    public function buildService(array $attributesService): self
    {
        $service            = new Service();
        $service->operator  = Operations::OI;
        $service->operation = Operations::OI_RESIDENCIAL;
        $service->mode      = Modes::ACTIVATION;
        $service->sector    = Operations::TELECOMMUNICATION;
        $service->status    = ServiceStatus::APPROVED;
        $service->label     = $attributesService['name'] ?? null;
        $service->price     = (float) ($attributesService['value'] ?? 0);
        $this->service      = $service;

        return $this;
    }

    public function buildSale(\DateTime $dateTime): self
    {
        $this->sale              = new Sale();
        $this->sale->user        = $this->user->toMongoAggregation();
        $this->sale->pointOfSale = (new PointOfSaleResource())->map($this->pointOfSale) ?? null;

        $this->sale->source  = SubSystemEnum::WEB;
        $this->sale->channel = Channels::VAREJO;
        $this->sale->setTransactionNumber();

        $this->service->serviceTransaction = $this->sale->saleTransaction . '-0';
        $this->service->customer           = $this->customer;
        $this->sale->services()->associate($this->service);
        $this->sale->total     = (float) $this->service->price;
        $this->sale->createdAt = $dateTime->format('Y-m-d H:i:s');

        return $this;
    }

    public function build(): Sale
    {
        return $this->sale;
    }
}
