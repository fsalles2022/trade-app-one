<?php

namespace FastShop\tests\Helpers\Builders;

use FastShop\Models\Product;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\Service;

class ProductBuilder
{
    private $code;
    private $service;

    public static function make(): ProductBuilder
    {
        return new self();
    }

    public function withCode(int $code) : ProductBuilder
    {
        $this->code = $code;
        return $this;
    }

    public function withService(Service $service) : ProductBuilder
    {
        $this->service = $service;
        return $this;
    }

    public function withRandomService() : ProductBuilder
    {
        $this->service = factory(Service::class)->create()->toArray();
        return $this;
    }

    public function build(): Product
    {
        $parameter = [];

        if ($this->code) {
            data_set($parameter, 'code', $this->code);
        }

        $service = factory(Service::class)->create();
        data_set($parameter, 'serviceId', $service->id);

        if ($this->service) {
            data_set($parameter, 'serviceId', $this->service->id);
        }

        return factory(Product::class)->create($parameter);
    }
}
