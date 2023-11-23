<?php

namespace TradeAppOne\Tests\Unit\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Mockery;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Exports\Operators\RegisterMailToOi;
use TradeAppOne\Mail\MailRegistrations;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class RegisterMailToOiCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();

        config([
            'utils.senderMails.OI.emails' => 'test@test.com',
            'utils.senderMails.OI.emailsCC' => 'test@test.com'
        ]);

        $mock = Mockery::mock(RegisterMailToOi::class)->makePartial();
        $mock->shouldReceive('build')
            ->andReturnUsing(function () {
                dispatch(new MailRegistrations(
                    '',
                    [],
                    [],
                    []
                ));

                return null;
            });

        $this->app->instance(RegisterMailToOi::class, $mock);
    }

    /** @test */
    public function should_dispatch_one_event(): void
    {
        Artisan::call('mail:oi', ['--networks' => [NetworkEnum::CEA]]);

        Queue::assertPushed(MailRegistrations::class, 1);
    }

    /** @test */
    public function should_dispatch_two_events(): void
    {
        $networkCea  = (new NetworkBuilder())->withSlug(NetworkEnum::CEA)->build();
        $pointOfSale = factory(PointOfSale::class)->create([
            'networkId' => $networkCea->id,
            'providerIdentifiers' => '{"OI": "1010985"}'
        ]);

        (new UserBuilder())->withNetwork($networkCea)->withPointOfSale($pointOfSale)->build();

        $networkTaqi  = (new NetworkBuilder())->withSlug(NetworkEnum::TAQI)->build();
        $pointOfSale = factory(PointOfSale::class)->create([
            'networkId' => $networkTaqi->id,
            'providerIdentifiers' => '{"OI": "1011054"}'
        ]);

        (new UserBuilder())->withNetwork($networkTaqi)->withPointOfSale($pointOfSale)->build();

        Artisan::call('mail:oi', ['--all' => true]);

        Queue::assertPushed(MailRegistrations::class, 2);
    }
}
