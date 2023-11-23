<?php

namespace TimBR\Tests\Unit\Services;

use TimBR\Services\TimBRMapPlansService;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Tests\TestCase;

class TimBRMapPlansServiceTest extends TestCase
{
    /** @test */
    public function should_return_no_loyalty()
    {
        $products = file_get_contents(__DIR__ . '/../../ServerTest/controleFaturaEligibilitySucess.json');
        $products = json_decode($products, true)['products'];

        $result  = TimBRMapPlansService::map($products, Operations::TIM_CONTROLE_FATURA);
        $timBPus = $result->where('product', '1-IL65OW');

        self::assertCount(2, $timBPus);
        self::assertArrayNotHasKey('loyalty', $timBPus->first());
    }

    /** @test */
    public function shouls_return_operation_express()
    {
        $products = file_get_contents(__DIR__ . '/../../ServerTest/controleExpressEligibilitySucess.json');
        $products = json_decode($products, true)['products'];

        $result = TimBRMapPlansService::map($products, Operations::TIM_EXPRESS);

        self::assertArrayHasKey('operation', $result->first());
        self::assertEquals(Operations::TIM_EXPRESS, $result->first()['operation']);
    }


    /** @test */
    public function shouls_return_null_when_fatura_not_ound()
    {
        $products = file_get_contents(__DIR__ . '/../../ServerTest/controleExpressEligibilitySucess.json');
        $products = json_decode($products, true)['products'];

        $result = TimBRMapPlansService::map($products, Operations::TIM_CONTROLE_FATURA);

        self::assertNull($result->first());
    }
}
