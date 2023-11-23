<?php

namespace VivoBR\Tests\Unit\Helpers;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use VivoBR\Helpers\VivoBrPlansFilter;
use VivoBR\Services\VivoBrMapPlansService;

class VivoBrPlansFilterTest extends TestCase
{

    /** @test */
    public function should_return_an_instance()
    {
        $class     = new VivoBrPlansFilter();
        $className = get_class($class);
        $this->assertEquals(VivoBrPlansFilter::class, $className);
    }

    /** @test */
    public function should_return_one_plans_when_filtered_by_operation()
    {
        $plans   = $this->getPlans();
        $options = ['operation' => Operations::VIVO_PRE];
        $user    = (new UserBuilder())->build();
        $result  = VivoBrPlansFilter::filter($plans, $options, $user);

        $this->assertCount(1, $result);
    }

    private function getPlans()
    {
        return VivoBrMapPlansService::map(
            json_decode('
        {
            "planos": [
            {
                "id": 1,
                "nome": "PRÉ PAGO",
                "ddd": 11,
                "valor": 0,
                "tipo": "PRE"
            },
            {
                "id": 1432,
                "nome": "VIVO CONTROLE DIGITAL - 2GB - R$44.99",
                "ddd": 11,
                "valor": 44.99,
                "tipo": "CONTROLE",
                "tipoFaturas": [
                    "VIA_POSTAL",
                    "EMAIL"
                ]
            }]
        }', true)
        );
    }
}
