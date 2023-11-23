<?php

namespace Outsourced\Cea\tests\Unit\Services;

use Outsourced\Cea\GiftCardConnection\CeaConnection;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Tests\TestCase;

class CeaServiceTest extends TestCase
{
    /** @test */
    public function should_return_activation_giftCard_with_status_activation()
    {
        $service  = factory(Service::class)->make();
        $response = $this->service()->activateGiftCard('10010907063026970', '50.00', '0000', $service->customer)->get();

        self::assertArrayHasKey('NumeroCartao', $response);
        self::assertArrayHasKey('Status', $response);
        self::assertArrayHasKey('ValorSaldo', $response);
        self::assertArrayHasKey('IDTransacao', $response);

        self::assertEquals('10010907063026970', $response['NumeroCartao']);
    }

    private function service(): CeaConnection
    {
        return resolve(CeaConnection::class);
    }
}
