<?php

namespace VivoBR\Tests\Unit\Adapters;

use TradeAppOne\Exceptions\BusinessExceptions\ServiceNotIntegrated;
use TradeAppOne\Tests\TestCase;
use VivoBR\Adapters\SunConfirmControleCartaoRequestAdapter;
use VivoBR\Models\VivoControle;
use VivoBR\Tests\Helpers\VivoFactoriesHelper;

class ConfirmControleBoletoRequestAdapterTest extends TestCase
{
    use VivoFactoriesHelper;

    /** @test */
    public function should_return_service_array_operator_identifiers_found()
    {
        $adapted = SunConfirmControleCartaoRequestAdapter::adapt($this->serviceAlreadyIntegrated());
        self::assertTrue(is_array($adapted));
    }

    public function serviceAlreadyIntegrated()
    {
        $service                      = $this->sunFactories()->of(VivoControle::class)->make();
        $service->operatorIdentifiers = [
            'idVenda'   => 'SP-123',
            'idServico' => '456123'
        ];
        return $service;
    }

    /** @test */
    public function should_return_default_reprovado_when_status_not_found()
    {
        $adapted = SunConfirmControleCartaoRequestAdapter::adapt($this->serviceAlreadyIntegrated());
        self::assertEquals('REPROVADO', $adapted['status']);
    }

    /** @test */
    public function should_return_aprovado_when_status_is_success()
    {
        $adapted = SunConfirmControleCartaoRequestAdapter::adapt(
            $this->serviceAlreadyIntegrated(),
            ['status' => 'SUCCESS']
        );
        self::assertEquals('APROVADO', $adapted['status']);
    }

    /** @test */
    public function should_return_reprovado_when_status_is_failed()
    {
        $adapted = SunConfirmControleCartaoRequestAdapter::adapt(
            $this->serviceAlreadyIntegrated(),
            ['status' => 'FAILED']
        );
        self::assertEquals('REPROVADO', $adapted['status']);
    }

    /** @test */
    public function should_return_service_not_integrated_exception_when_operator_identifiers_not_found()
    {
        $this->expectException(ServiceNotIntegrated::class);
        SunConfirmControleCartaoRequestAdapter::adapt($this->serviceNotIntegrated());
    }

    public function serviceNotIntegrated()
    {
        return $this->sunFactories()->of(VivoControle::class)->make();
    }
}
