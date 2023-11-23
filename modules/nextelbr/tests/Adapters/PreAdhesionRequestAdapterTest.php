<?php

namespace NextelBR\Tests\Adapters;

use NextelBR\Adapters\Request\PreAdhesionRequestAdapter;
use NextelBR\Enumerators\NextelInvoiceTypes;
use NextelBR\Exceptions\PlanNotEligible;
use NextelBR\Models\NextelBRControleBoleto;
use NextelBR\Models\NextelBRControleCartao;
use NextelBR\Tests\Helpers\NextelBRFactories;
use TradeAppOne\Tests\TestCase;

class PreAdhesionRequestAdapterTest extends TestCase
{
    use NextelBRFactories;

    /** @test */
    public function should_return_controle_m4u()
    {
        $cartao  = $this->factory()->of(NextelBRControleCartao::class)->make([
            'product' => '123',
            'offer'   => '123'
        ]);
        $extra   = [
            'cachedInformations' => [
                'plans'   => collect([['product' => '123', 'offer' => '123']]),
                'address' => []
            ]
        ];
        $adapted = PreAdhesionRequestAdapter::adapt($cartao, $extra);
        self::assertEquals(NextelInvoiceTypes::CARTAO_M4U, $adapted['formaPagamento']);
    }

    /** @test */
    public function should_return_debito()
    {
        $cartao  = $this->factory()->of(NextelBRControleBoleto::class)->states('directDebit')->make([
            'product' => '123',
            'offer'   => '123'
        ]);
        $extra   = [
            'cachedInformations' => [
                'plans'   => collect([['product' => '123', 'offer' => '123']]),
                'address' => []
            ]
        ];
        $adapted = PreAdhesionRequestAdapter::adapt($cartao, $extra);
        self::assertEquals(NextelInvoiceTypes::DEBITO_AUTOMATICO_REQUEST, $adapted['formaPagamento']);
    }

    /** @test */
    public function should_return_plan_not_found()
    {
        $cartao = $this->factory()->of(NextelBRControleBoleto::class)->states('directDebit')->make([
            'product' => '123',
            'offer'   => '123'
        ]);
        $extra  = [
            'cachedInformations' => [
                'plans'   => collect([['product' => '000', 'offer' => '123']]),
                'address' => []
            ]
        ];
        $this->expectException(PlanNotEligible::class);
        $adapted = PreAdhesionRequestAdapter::adapt($cartao, $extra);
        self::assertEquals(NextelInvoiceTypes::DEBITO_AUTOMATICO_REQUEST, $adapted['formaPagamento']);
    }

    /** @test */
    public function should_replace_generic_address()
    {
        $cartao  = $this->factory()->of(NextelBRControleBoleto::class)->states('directDebit')->make([
            'product' => '123',
            'offer'   => '123'
        ]);
        $extra   = [
            'cachedInformations' => [
                'plans' => collect([['product' => '123', 'offer' => '123']]),
            ],
            'address'            => ['logradouro' => null, 'bairro' => null]
        ];
        $adapted = PreAdhesionRequestAdapter::adapt($cartao, $extra);
        self::assertNotNull($adapted['endereco']['logradouro']);
        self::assertNotNull($adapted['endereco']['bairro']);
    }

    /** @test */
    public function should_not_replace_generic_address()
    {
        $cartao  = $this->factory()->of(NextelBRControleBoleto::class)->states('directDebit')->make([
            'product' => '123',
            'offer'   => '123'
        ]);
        $extra   = [
            'cachedInformations' => [
                'plans' => collect([['product' => '123', 'offer' => '123']]),
            ],
            'address'            => ['logradouro' => 'Replace', 'bairro' => 'Replace']
        ];
        $adapted = PreAdhesionRequestAdapter::adapt($cartao, $extra);
        self::assertEquals('Replace', $adapted['endereco']['logradouro']);
        self::assertEquals('Replace', $adapted['endereco']['bairro']);
    }


    /** @test */
    public function should_not_replace_empty_address()
    {
        $cartao  = $this->factory()->of(NextelBRControleBoleto::class)->states('directDebit')->make([
            'product' => '123',
            'offer'   => '123'
        ]);
        $extra   = [
            'cachedInformations' => [
                'plans' => collect([['product' => '123', 'offer' => '123']]),
            ],
            'address'            => []
        ];
        $adapted = PreAdhesionRequestAdapter::adapt($cartao, $extra);
        self::assertNotNull('Replace', $adapted['endereco']['logradouro']);
        self::assertNotNull('Replace', $adapted['endereco']['bairro']);
        self::assertNotEmpty('Replace', $adapted['endereco']['bairro']);
    }
}
