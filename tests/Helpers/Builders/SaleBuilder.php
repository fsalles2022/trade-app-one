<?php

namespace TradeAppOne\Tests\Helpers\Builders;

use TradeAppOne\Domain\Enumerators\SaleChannels;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Http\Resources\PointOfSaleResource;
use TradeAppOne\Http\Resources\UserResource;

/**
 * @property PointOfSale pointOfSale
 * @property User user
 * @property array services
 */
class SaleBuilder
{
    private $pointOfSale;
    private $services;
    private $user;
    private $source;

    public static function make(): SaleBuilder
    {
        return new self();
    }

    public function withUser(User $user): SaleBuilder
    {
        $this->user = $user;

        return $this;
    }

    public function withServices($services): SaleBuilder
    {
        $this->services = array_wrap($services);

        return $this;
    }

    public function withPointOfSale(PointOfSale $pointOfSale): SaleBuilder
    {
        $this->pointOfSale = $pointOfSale;

        return $this;
    }

    public function withSource(string $source): SaleBuilder
    {
        $this->source = $source;

        return $this;
    }

    public function build(): Sale
    {
        $this->defineUserAndPointOfSaleOptions();
        $totalPrice      = 0;

        $saleTransaction = (new Sale())->setTransactionNumber()['saleTransaction'];
        $index = 0;
        $sale = new Sale();
        $sale->save();

        foreach ($this->services as $service) {
            $totalPrice += $service->price;
            $service->serviceTransaction = $saleTransaction . '-' . $index;
            $index++;
            $sale->services()->associate($service);
        }

        $sale->forceFill(
            [
                'source'          => $this->source ?? 'WEB',
                'channel'         => SaleChannels::VAREJO,
                'user'            => UserResource::make($this->user)->resolve(),
                'pointOfSale'     => (new PointOfSaleResource())->map($this->pointOfSale),
                'saleTransaction' => $saleTransaction,
                'price'           => $totalPrice
            ]
        );

        $sale->save();

        return $sale;
    }

    private function defineUserAndPointOfSaleOptions(): void
    {
        if (is_null($this->user)) {
            $pointOfSale       = $this->pointOfSale ?? (new PointOfSaleBuilder())->build();
            $this->user        = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
            $this->pointOfSale = $pointOfSale;
        }

        if (is_null($this->pointOfSale)) {
            $this->pointOfSale = $pointOfSale = (new PointOfSaleBuilder())->build();
        }
    }
}
