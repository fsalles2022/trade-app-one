<?php

namespace TradeAppOne\Tests\Feature\Sale;

use Gateway\tests\Helpers\GatewayFactoriesHelper;
use Illuminate\Contracts\Bus\Dispatcher;
use McAfee\Models\McAfeeMobileSecurity;
use McAfee\Tests\Helpers\McAfeeFactoriesHelper;
use Mockery;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class NetworkHooksFeatureTest extends TestCase
{
    use AuthHelper, McAfeeFactoriesHelper, GatewayFactoriesHelper;

    /** @test */
    public function should_register_job_when_networks_is_CEA()
    {
        $mock = Mockery::mock(Dispatcher::class)->makePartial();
        $mock->shouldReceive('dispatchNow')->never()->andReturnNull();
        $this->app->bind(Dispatcher::class, $mock);

        $service = $this->buildSaleCea();

        $this->authAs()->put('sales/', [
            'serviceTransaction' => $service->serviceTransaction, 'creditCard' => $this->payloadCreditCard()
        ]);
    }

    private function buildSaleCea(): Service
    {
        //Build Sale McAfee MobileSecurity to CEA
        $network     = factory(Network::class)->create(['slug' => NetworkEnum::CEA]);
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $service     = $this->mcAfeeFactories()->of(McAfeeMobileSecurity::class)->make();
        $sale        = (new SaleBuilder())->withServices([$service])->withPointOfSale($pointOfSale)->build();
        return $sale->services->first();
    }
}