<?php

namespace VivoBR\Tests\Unit\Services;

use TradeAppOne\Tests\TestCase;
use VivoBR\Models\VivoControleCartao;
use VivoBR\Services\VivoContest;
use VivoBR\Tests\Helpers\VivoFactoriesHelper;

class VivoContestTest extends TestCase
{
    use VivoFactoriesHelper;

    /** @test */
    public function should_return_success()
    {
        $service = $this->sunFactories()->of(VivoControleCartao::class)->make([
            'operatorIdentifiers' => [
                'idVenda'   => '',
                'idServico' => ''
            ]
        ])->toArray();
        $sale    = $this->saleFactory([$service]);

        $contest = resolve(VivoContest::class);
        $result  = $contest->contestService($sale->services[0]);
        self::assertArrayHasKey('message', $result);
        self::assertArrayHasKey('service', $result);
    }
}
