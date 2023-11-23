<?php

namespace TradeAppOne\Tests\Helpers\Builders;

use TradeAppOne\Domain\Models\Tables\AvailableService;
use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Domain\Models\Tables\User;

class PointOfSaleBuilder
{
    private $hierarchy;
    private $network;
    private $user;
    private $state;
    private $params = [];
    private $service;

    public static function make(): PointOfSaleBuilder
    {
        return new self();
    }

    public function withUser(User $user) : PointOfSaleBuilder
    {
        $this->user = $user;

        return $this;
    }

    public function withHierarchy(Hierarchy $hierarchy) : PointOfSaleBuilder
    {
        $this->hierarchy = $hierarchy;

        return $this;
    }

    public function withNetwork(Network $network) : PointOfSaleBuilder
    {
        $this->network = $network;

        return $this;
    }

    public function withState($state): PointOfSaleBuilder
    {
        $this->state = $state;
        return $this;
    }

    public function withParams(array $params = []): PointOfSaleBuilder
    {
        $this->params = $params;
        return $this;
    }

    public function withRandomServices(int $amount = 5) : PointOfSaleBuilder
    {
        $this->withServices(factory(Service::class)->times($amount)->create()->toArray());

        return $this;
    }

    /**
     * @param Service|array $service
     * @return $this
     */
    public function withServices($service = []) : PointOfSaleBuilder
    {
        $this->service = array_pluck(array_wrap($service), 'id');

        return $this;
    }

    public function withService(Service $service): PointOfSaleBuilder
    {
        $this->service = $service->id;

        return $this;
    }

    public function generateTimes(int $quantity)
    {
        $builded = collect();
        for ($i = 0; $i < $quantity; $i++) {
            $builded->push($this->build());
        }
        return $builded;
    }

    public function build() : PointOfSale
    {
        $pointOfSale    = $this->state ?
            factory(PointOfSale::class)->states($this->state)->make($this->params) :
            factory(PointOfSale::class)->make($this->params);
        $networkFactory = $this->network ? $this->network : factory(Network::class)->create();

        $this->service && $pointOfSale->services()->sync($this->service);
        $this->service && $networkFactory->services()->sync($this->service);

        $this->hierarchy && $pointOfSale->hierarchy()->associate($this->hierarchy);

        $pointOfSale->network()->associate($networkFactory);
        $pointOfSale->save();

        $this->user && $this->user->pointsOfSale()->attach($pointOfSale);

        $this->service && AvailableService::create([
            'serviceId'     => data_get($this->service, '0'),
            'pointOfSaleId' => $pointOfSale->id
        ]);

        return $pointOfSale;
    }
}
