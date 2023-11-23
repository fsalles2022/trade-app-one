<?php

namespace Outsourced\Cea\tests\Unit\Services;

use McAfee\Models\McAfeeMobileSecurity;
use McAfee\Tests\Helpers\McAfeeFactoriesHelper;
use Outsourced\Cea\Components\CeaGiftCardActivationResponse;
use Outsourced\Cea\Exceptions\CeaExceptions;
use Outsourced\Cea\GiftCardConnection\CeaConnection;
use Outsourced\Cea\Hooks\CeaHooks;
use Outsourced\Cea\Models\CeaGiftCard;
use Outsourced\Cea\tests\ServerTest\GiftCard\CeaResponseMock;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Tests\TestCase;

class CeaGiftCardServiceTest extends TestCase
{
    use McAfeeFactoriesHelper;

    /** @test */
    public function should_return_null_when_service_is_not_activated()
    {
        $service = $this->mcAfeeFactories()
            ->of(McAfeeMobileSecurity::class)
            ->make([
                'status' => ServiceStatus::CANCELED,
                'operator' => Operations::TRADE_IN
            ]);

        $received = $this->service()->execute($service);

        $this->assertNull($received);
    }


    public function should_return_service_with_code_gift_card_when_is_tradeIn()
    {
        $giftCard = factory(CeaGiftCard::class)->create();

        $service = $this->mcAfeeFactories()
            ->of(McAfeeMobileSecurity::class)
            ->make([
                'serviceTransaction' => '123456',
                'status' => ServiceStatus::APPROVED,
                'operator' => Operations::TRADE_IN_MOBILE
            ]);

        $received = $this->service()->execute($service);

        $this->assertDatabaseHas('cea_gift_cards', [
            'code'  => $giftCard->code,
            'value' => $service->price,
            'outsourcedId' => CeaResponseMock::ID_TRANSACAO,
            'reference' => '123456',
            'partner' => CeaHooks::PARTNER_TRADE_IN,
            'deletedAt' => now()->toDateTimeString()
        ], 'outsourced');

        $this->assertEquals($giftCard->code, $received->register['card']);
    }


    public function should_return_service_with_code_gift_card_when_is_triangulation()
    {
        $giftCard = factory(CeaGiftCard::class)->create([
            'partner'=> CeaHooks::PARTNER_TRIANGULATION
        ]);

        $service = factory(Service::class)->make([
            'serviceTransaction' => '123456',
            'operator' => Operations::OI,
            'status' => ServiceStatus::APPROVED,
            'sector' => Operations::TELECOMMUNICATION,
            'discount' => [
                'id' => 12,
                'discount' => '200'
            ]
        ]);

        $received = $this->service()->execute($service);

        $this->assertDatabaseHas('cea_gift_cards', [
            'id' => $giftCard->id,
            'code'  => $giftCard->code,
            'value' => 200.0,
            'partner' => CeaHooks::PARTNER_TRIANGULATION,
            'outsourcedId' => CeaResponseMock::ID_TRANSACAO,
            'reference' => '123456',
            'deletedAt' => now()->toDateTimeString()
        ], 'outsourced');

        $this->assertEquals($giftCard->code, $received->register['card']);
    }


    public function should_not_save_IDTrasacao_when_card_is_not_activated()
    {
        $giftCard = factory(CeaGiftCard::class)->create();

        $mockResponse = \Mockery::mock(CeaGiftCardActivationResponse::class)->makePartial();
        $mockResponse->shouldReceive('isActivated')->once()->andReturnFalse();
        $mockResponse->shouldReceive('getIDTransacao')->never();

        $mock = \Mockery::mock(CeaConnection::class)->makePartial();
        $mock->shouldReceive('activateGiftCard')->once()->andReturn($mockResponse);
        $this->app->instance(CeaConnection::class, $mock);

        $service = $this->mcAfeeFactories()
            ->of(McAfeeMobileSecurity::class)
            ->make([
                'serviceTransaction' => '123456',
                'status' => ServiceStatus::APPROVED,
                'operator' => Operations::TRADE_IN_MOBILE
            ]);

        $received = $this->service()->execute($service);

        $this->assertDatabaseMissing('cea_gift_cards', [
            'code'  => $giftCard->code,
            'outsourcedId' => CeaResponseMock::ID_TRANSACAO,
        ], 'outsourced');

        $this->assertNull($received->register['card']);
        $this->assertNotNull($giftCard->refresh()->deletedAt);
    }

    /** @test */
    public function should_return_null_when_services_is_not_tradeIn_and_triangulation()
    {
        $service = factory(Service::class)->make([
            'status' => ServiceStatus::APPROVED,
            'operator' => Operations::MCAFEE
        ]);

        $received = $this->service()->execute($service);
        $this->assertNull($received);
    }


    public function sould_return_exception_when_no_card_available()
    {
        factory(CeaGiftCard::class)->create();

        $service = $this->mcAfeeFactories()
            ->of(McAfeeMobileSecurity::class)
            ->make([
                'serviceTransaction' => '123456',
                'status' => ServiceStatus::APPROVED,
                'operator' => Operations::TRADE_IN_MOBILE
            ]);

        $this->service()->execute($service);

        $service2 = $this->mcAfeeFactories()
            ->of(McAfeeMobileSecurity::class)
            ->make([
                'serviceTransaction' => '123456',
                'status' => ServiceStatus::APPROVED,
                'operator' => Operations::TRADE_IN_MOBILE
            ]);

        $this->expectExceptionCode(CeaExceptions::CARD_UNAVAILABLE);

        $this->service()->execute($service2);
    }

    private function service(): CeaHooks
    {
        return resolve(CeaHooks::class);
    }
}
