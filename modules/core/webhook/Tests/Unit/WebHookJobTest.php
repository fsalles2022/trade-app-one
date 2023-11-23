<?php

namespace Core\WebHook\Tests\Unit;

use Core\WebHook\Connections\Clients\WebHookHttpClient;
use Core\WebHook\Jobs\WebHookJob;
use TradeAppOne\Domain\Enumerators\Channels;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\Channel;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class WebHookJobTest extends TestCase
{
    /** @test */
    public function should_send_changes()
    {
        $this->app->singleton(WebHookHttpClient::class, static function () {
            $mock = \Mockery::mock(WebHookHttpClient::class)->makePartial();
            $mock->shouldReceive('send')->once()->andReturnNull();

            return $mock;
        });

        $network = factory(Network::class)->create([
            'channel' => Channels::DISTRIBUICAO
        ]);

        $channel = factory(Channel::class)->create([
            'name' => Channels::DISTRIBUICAO
        ]);

        $network->channels()->detach();
        $network->channels()->attach($channel);

        $pointOfSale = PointOfSaleBuilder::make()
            ->withNetwork($network)
            ->build();

        $service = factory(Service::class)->make([
            'sector' => Operations::LINE_ACTIVATION
        ]);

        $sale = SaleBuilder::make()
            ->withPointOfSale($pointOfSale)
            ->withServices($service)
            ->build();

        $serviceTransaction = $sale->services->first()->serviceTransaction;
        $changes            = ['status' => 'FAKE'];

        (new WebHookJob($serviceTransaction, $changes))->handle();
    }
}
