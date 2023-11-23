<?php

namespace TradeAppOne\Tests\Unit\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Reports\Tests\Helpers\BindInstance;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Exports\Operators\RegisterMailToOi;
use TradeAppOne\Mail\MailRegistrations;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class RegisterMailToNextelCommandTest extends TestCase
{
    use BindInstance;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();

        $mock = Mockery::mock(RegisterMailToOi::class)->makePartial();
        $mock->shouldReceive('build')->never()->andReturnNull();
        $this->app->instance(RegisterMailToOi::class, $mock);
    }

    /** @test */
    public function should_dispatch_one_event(): void
    {
        Artisan::call('mail:nextel', ['--networks' => [NetworkEnum::CEA]]);

        Queue::assertPushed(MailRegistrations::class, 1);
    }

    /** @test */
    public function should_dispatch_two_events(): void
    {
        $networkCea  = (new NetworkBuilder())->withSlug(NetworkEnum::CEA)->build();
        $pointOfSale = factory(PointOfSale::class)->create([
            'networkId' => $networkCea->id,
            'providerIdentifiers' => '{"NEXTEL": {"cod": "19859", "ref": "72887"}'
        ]);

        (new UserBuilder())->withNetwork($networkCea)->withPointOfSale($pointOfSale)->build();

        $networkTaqi  = (new NetworkBuilder())->withSlug(NetworkEnum::TAQI)->build();
        $pointOfSale = factory(PointOfSale::class)->create([
            'networkId' => $networkTaqi->id,
            'providerIdentifiers' => '{"NEXTEL": {"cod": "19097", "ref": "72877"}'
        ]);

        (new UserBuilder())->withNetwork($networkTaqi)->withPointOfSale($pointOfSale)->build();

        Artisan::call('mail:nextel', ['--all' => true]);

        Queue::assertPushed(MailRegistrations::class, 2);
    }
}
