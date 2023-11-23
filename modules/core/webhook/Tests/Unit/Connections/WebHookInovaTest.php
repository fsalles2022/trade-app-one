<?php

namespace Core\WebHook\Tests\Unit\Connections;

use Core\WebHook\Connections\Clients\WebHookHttpClient;
use Core\WebHook\Connections\WebHookInova;
use TradeAppOne\Domain\Enumerators\Channels;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\Channel;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class WebHookInovaTest extends TestCase
{
    /** @test */
    public function should_send_changes_when_exists_changes(): void
    {
        $mock = \Mockery::mock(WebHookHttpClient::class)->makePartial();
        $mock->shouldReceive('send')->once()->andReturnNull();

        $service = factory(Service::class)->make();
        $sale    = SaleBuilder::make()->withServices($service)->build();

        $service = $sale->services->first();
        $changes = ['status' => 'FAKE'];

        (new WebHookInova($mock))->push($service, $changes);
    }

    /** @test */
    public function should_not_send_changes_when_no_changes(): void
    {
        $mock = \Mockery::mock(WebHookHttpClient::class)->makePartial();
        $mock->shouldReceive('send')->never()->andReturnNull();

        $service = factory(Service::class)->make();
        $sale    = SaleBuilder::make()->withServices($service)->build();

        $service = $sale->services->first();
        $changes = ['KEY_FAKE' => 'FAKE'];

        (new WebHookInova($mock))->push($service, $changes);
    }

    /** @test */
    public function should_return_true_when_service_belongs_to_inova(): void
    {
        $channel = factory(Channel::class)->states(Channels::DISTRIBUICAO)->create();
        $network = factory(Network::class)->create();
        $network->channels()->detach();
        $network->channels()->attach($channel);

        $pointOfSale = PointOfSaleBuilder::make()
            ->withNetwork($network)
            ->build();

        $service = factory(Service::class)->make([
            'sector' => Operations::LINE_ACTIVATION,
        ]);

        $sale = SaleBuilder::make()
            ->withPointOfSale($pointOfSale)
            ->withServices($service)
            ->build();

        $service  = $sale->services->first();
        $received = resolve(WebHookInova::class)->isForMe($service);

        $this->assertTrue($received);
    }

    /** @test */
    public function should_return_false_when_PDV_belongs_to_distribution(): void
    {
        $network = factory(Network::class)->create();

        $hierarchy = HierarchyBuilder::make()
            ->withSlug(NetworkEnum::INOVA)
            ->build();

        $hierarchyToPdv = HierarchyBuilder::make()
            ->withParent($hierarchy)
            ->withNetwork($network)
            ->build();

        $pointOfSale = PointOfSaleBuilder::make()
            ->withHierarchy($hierarchyToPdv)
            ->build();

        $service = factory(Service::class)->make();
        $sale    = SaleBuilder::make()
            ->withPointOfSale($pointOfSale)
            ->withServices($service)
            ->build();

        $service = $sale->services->first();

        $received = resolve(WebHookInova::class)->isForMe($service);

        $this->assertFalse($received);
    }
}
