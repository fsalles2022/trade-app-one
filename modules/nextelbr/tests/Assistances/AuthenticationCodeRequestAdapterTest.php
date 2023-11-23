<?php

namespace NextelBR\Tests\Assistances;

use NextelBR\Adapters\Request\AuthenticationCodeRequestAdapter;
use NextelBR\Tests\Helpers\NextelBRFactories;
use NextelBR\Models\NextelBRControleCartao;
use TradeAppOne\Tests\TestCase;

class AuthenticationCodeRequestAdapterTest extends TestCase
{
    use NextelBRFactories;

    /** @test */
    public function should_return_with_protocol()
    {
        $controleCartao = $this->factory()->of(NextelBRControleCartao::class)->make();
        $adapted        = AuthenticationCodeRequestAdapter::adapt($controleCartao);
        self::assertArrayHasKey('protocol', $adapted);
    }

    /** @test */
    public function should_return_with_channel()
    {
        $controleCartao = $this->factory()->of(NextelBRControleCartao::class)->make();
        $adapted        = AuthenticationCodeRequestAdapter::adapt($controleCartao);
        self::assertArrayHasKey('channel', $adapted);
    }

    /** @test */
    public function should_return_with_cpf()
    {
        $controleCartao = $this->factory()->of(NextelBRControleCartao::class)->make();
        $adapted        = AuthenticationCodeRequestAdapter::adapt($controleCartao);
        self::assertArrayHasKey('cpf', $adapted);
    }
}
