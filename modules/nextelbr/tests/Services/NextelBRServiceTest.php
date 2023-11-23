<?php

namespace NextelBR\Tests\Services;

use NextelBR\Assistance\OperationAssistances\NextelBRControleCartaoAssistance;
use NextelBR\Connection\NextelBR\NextelBRConnection;
use NextelBR\Models\NextelBRControleCartao;
use NextelBR\Services\NextelBRService;
use NextelBR\Tests\Helpers\NextelBRFactories;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\ServiceTransactionGenerator;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class NextelBRServiceTest extends TestCase
{
    use NextelBRFactories;

    /** @test */
    public function should_return_portability_plans()
    {
        $planos      = [
            "planos" => [
                [
                    "idPlano"   => "2250|1532440925230",
                    "idOferta"  => "2250|4331|1532440925230",
                    "descricao" => "Controle Ilimitado 2GB + 500MB Bonus GV",
                    "nomePlano" => "CTR 2GB+ilimitado 194/PoS/SMP",
                    "tabelas"   => [
                        [
                            "idTabela"             => "4331",
                            "nomeTabela"           => "Controle Ilimitado 2GB + 500MB Bonus GV",
                            "valorTotal"           => 5900,
                            "valorComDesconto"     => 3999,
                            "periodoFidelizacao"   => 12,
                            "periodoDescontoPlano" => 12,
                            "periodoDescontoBonus" => 12,
                            "bonusInternet"        => "500MB",
                            "franquiaDeVoz"        => "ILIMITADO",
                            "franquiaDeDados"      => "2GB",
                            "formasDePagamento"    => ["DEBITO_AUTOMATICO", "BOLETO"],
                            "portabilidade"        => false,
                            "fidelizacao"          => true,
                            "valorThab"            => 2500
                        ]
                    ]
                ]
            ]
        ];
        $saleService = \Mockery::mock(SaleService::class)->makePartial();
        $assistance  = \Mockery::mock(NextelBRControleCartaoAssistance::class)->makePartial();
        $saleService->shouldReceive('pushLogService')->atLeast();
        $saleService->shouldReceive('updateStatusService')->atLeast();
        $response      = \Mockery::mock(Responseable::class)->makePartial();
        $responsePlans = \Mockery::mock(Responseable::class)->makePartial();
        $connection    = \Mockery::mock(NextelBRConnection::class)->makePartial();

        $response->shouldReceive('toArray')->andReturn([
            "protocolo"      => "201700360970534",
            "numeroPedido"   => "1800158134",
            "score"          => "1.000,12",
            "scoreDescricao" => "I"
        ]);
        $connection->shouldReceive('eligibility')->andReturn($response);

        $responsePlans->shouldReceive('toArray')->andReturn($planos);
        $connection->shouldReceive('getPlans')->andReturn($responsePlans);

        $user    = $this->userNextel();
        $service = new NextelBRService($connection, $saleService, $assistance);
        $result  = $service->eligibility(['areaCode' => 11], $user);
        self::assertNotEmpty($result);
    }

    private function userNextel()
    {
        $pointOfSale = $this->pointOfSaleNextel();
        return (new UserBuilder())->withPointOfSale($pointOfSale)->build();
    }

    /** @test */
    public function should_return_only_boleto()
    {
        $service = resolve(NextelBRService::class);
        $result  = $service->getEligiblePlans('I', ['operation' => Operations::NEXTEL_CONTROLE_BOLETO]);
        self::assertCount(0, $result->where('operation', '!=', Operations::NEXTEL_CONTROLE_BOLETO));
    }

    /** @test */
    public function should_return_only_cartao()
    {
        $service = resolve(NextelBRService::class);
        $result  = $service->getEligiblePlans('I', ['operation' => Operations::NEXTEL_CONTROLE_CARTAO]);
        self::assertCount(0, $result->where('operation', '!=', Operations::NEXTEL_CONTROLE_CARTAO));
    }

    /** @test */
    public function should_return_price_plans_with_cents_format()
    {
        $planos        = [
            "planos" => [
                [
                    "idPlano"   => "2250|1532440925230",
                    "idOferta"  => "2250|4331|1532440925230",
                    "descricao" => "Controle Ilimitado 2GB + 500MB Bonus GV",
                    "nomePlano" => "CTR 2GB+ilimitado 194/PoS/SMP",
                    "tabelas"   => [
                        [
                            "idTabela"             => "4331",
                            "nomeTabela"           => "Controle Ilimitado 2GB + 500MB Bonus GV",
                            "valorTotal"           => 5900,
                            "valorComDesconto"     => 3999,
                            "periodoFidelizacao"   => 12,
                            "periodoDescontoPlano" => 12,
                            "periodoDescontoBonus" => 12,
                            "bonusInternet"        => "500MB",
                            "franquiaDeVoz"        => "ILIMITADO",
                            "franquiaDeDados"      => "2GB",
                            "formasDePagamento"    => ["DEBITO_AUTOMATICO", "BOLETO"],
                            "portabilidade"        => false,
                            "fidelizacao"          => true,
                            "valorThab"            => 2500
                        ]
                    ]
                ]
            ]
        ];
        $saleService   = \Mockery::mock(SaleService::class)->makePartial();
        $response      = \Mockery::mock(Responseable::class)->makePartial();
        $responsePlans = \Mockery::mock(Responseable::class)->makePartial();
        $connection    = \Mockery::mock(NextelBRConnection::class)->makePartial();
        $assistance    = \Mockery::mock(NextelBRControleCartaoAssistance::class)->makePartial();

        $response->shouldReceive('toArray')->andReturn([
            "protocolo"      => "201700360970534",
            "numeroPedido"   => "1800158134",
            "score"          => "1.000,12",
            "scoreDescricao" => "I"
        ]);
        $connection->shouldReceive('eligibility')->andReturn($response);

        $responsePlans->shouldReceive('toArray')->andReturn($planos);
        $connection->shouldReceive('getPlans')->andReturn($responsePlans);

        $service = new NextelBRService($connection, $saleService, $assistance);
        $user    = $this->userNextel();
        $result  = $service->eligibility(['areaCode' => 11], $user);
        self::assertEquals(3999 / 100, $result->first()['price']);
    }

    /** @test */
    public function should_return_adhesion_price_plans_with_cents_format()
    {
        $planos        = [
            "planos" => [
                [
                    "idPlano"   => "2250|1532440925230",
                    "idOferta"  => "2250|4331|1532440925230",
                    "descricao" => "Controle Ilimitado 2GB + 500MB Bonus GV",
                    "nomePlano" => "CTR 2GB+ilimitado 194/PoS/SMP",
                    "tabelas"   => [
                        [
                            "idTabela"             => "4331",
                            "nomeTabela"           => "Controle Ilimitado 2GB + 500MB Bonus GV",
                            "valorTotal"           => 5900,
                            "valorComDesconto"     => 3999,
                            "periodoFidelizacao"   => 12,
                            "periodoDescontoPlano" => 12,
                            "periodoDescontoBonus" => 12,
                            "bonusInternet"        => "500MB",
                            "franquiaDeVoz"        => "ILIMITADO",
                            "franquiaDeDados"      => "2GB",
                            "formasDePagamento"    => ["DEBITO_AUTOMATICO", "BOLETO"],
                            "portabilidade"        => false,
                            "fidelizacao"          => true,
                            "valorThab"            => 2500
                        ]
                    ]
                ]
            ]
        ];
        $saleService   = \Mockery::mock(SaleService::class)->makePartial();
        $response      = \Mockery::mock(Responseable::class)->makePartial();
        $responsePlans = \Mockery::mock(Responseable::class)->makePartial();
        $connection    = \Mockery::mock(NextelBRConnection::class)->makePartial();
        $assistance    = \Mockery::mock(NextelBRControleCartaoAssistance::class)->makePartial();

        $response->shouldReceive('toArray')->andReturn([
            "protocolo"      => "201700360970534",
            "numeroPedido"   => "1800158134",
            "score"          => "1.000,12",
            "scoreDescricao" => "I"
        ]);
        $connection->shouldReceive('eligibility')->andReturn($response);

        $responsePlans->shouldReceive('toArray')->andReturn($planos);
        $connection->shouldReceive('getPlans')->andReturn($responsePlans);

        $service = new NextelBRService($connection, $saleService, $assistance);
        $user    = $this->userNextel();
        $result  = $service->eligibility(['areaCode' => 11], $user);
        self::assertEquals(2500 / 100, $result->first()['adhesionValue']);
    }

    /** @test */
    public function should_returntwo_flatten_plans()
    {
        $planos        = [
            "planos" => [
                [
                    "idPlano"   => "2250|1532440925230",
                    "idOferta"  => "2250|4331|1532440925230",
                    "descricao" => "Controle Ilimitado 2GB + 500MB Bonus GV",
                    "nomePlano" => "CTR 2GB+ilimitado 194/PoS/SMP",
                    "tabelas"   => [
                        [
                            "idTabela"             => "4331",
                            "nomeTabela"           => "Controle Ilimitado 2GB + 500MB Bonus GV",
                            "valorTotal"           => 5900,
                            "valorComDesconto"     => 3999,
                            "periodoFidelizacao"   => 12,
                            "periodoDescontoPlano" => 12,
                            "periodoDescontoBonus" => 12,
                            "bonusInternet"        => "500MB",
                            "franquiaDeVoz"        => "ILIMITADO",
                            "franquiaDeDados"      => "2GB",
                            "formasDePagamento"    => ["DEBITO_AUTOMATICO", "BOLETO"],
                            "portabilidade"        => false,
                            "fidelizacao"          => true,
                            "valorThab"            => 2500
                        ],
                        [
                            "idTabela"             => "4331",
                            "nomeTabela"           => "Controle Ilimitado 2GB + 500MB Bonus GV",
                            "valorTotal"           => 5900,
                            "valorComDesconto"     => 3999,
                            "periodoFidelizacao"   => 12,
                            "periodoDescontoPlano" => 12,
                            "periodoDescontoBonus" => 12,
                            "bonusInternet"        => "500MB",
                            "franquiaDeVoz"        => "ILIMITADO",
                            "franquiaDeDados"      => "2GB",
                            "formasDePagamento"    => ["DEBITO_AUTOMATICO", "BOLETO"],
                            "portabilidade"        => false,
                            "fidelizacao"          => true,
                            "valorThab"            => 2500
                        ]
                    ]
                ]
            ]
        ];
        $saleService   = \Mockery::mock(SaleService::class)->makePartial();
        $response      = \Mockery::mock(Responseable::class)->makePartial();
        $responsePlans = \Mockery::mock(Responseable::class)->makePartial();
        $connection    = \Mockery::mock(NextelBRConnection::class)->makePartial();
        $assistance    = \Mockery::mock(NextelBRControleCartaoAssistance::class)->makePartial();

        $response->shouldReceive('toArray')->andReturn([
            "protocolo"      => "201700360970534",
            "numeroPedido"   => "1800158134",
            "score"          => "1.000,12",
            "scoreDescricao" => "I"
        ]);
        $connection->shouldReceive('eligibility')->andReturn($response);

        $responsePlans->shouldReceive('toArray')->andReturn($planos);
        $connection->shouldReceive('getPlans')->andReturn($responsePlans);

        $service = new NextelBRService($connection, $saleService, $assistance);
        $user    = $this->userNextel();

        $result = $service->eligibility(['areaCode' => 11], $user);
        self::assertCount(2, $result);
    }

    /** @test */
    public function should_return_portability_filtered_plans_with_cents_format()
    {
        $planos        = [
            "planos" => [
                [
                    "idPlano"   => "2250|1532440925230",
                    "idOferta"  => "2250|4331|1532440925230",
                    "descricao" => "Controle Ilimitado 2GB + 500MB Bonus GV",
                    "nomePlano" => "CTR 2GB+ilimitado 194/PoS/SMP",
                    "tabelas"   => [
                        [
                            "idTabela"             => "4331",
                            "nomeTabela"           => "Controle Ilimitado 2GB + 500MB Bonus GV",
                            "valorTotal"           => 5900,
                            "valorComDesconto"     => 3999,
                            "periodoFidelizacao"   => 12,
                            "periodoDescontoPlano" => 12,
                            "periodoDescontoBonus" => 12,
                            "bonusInternet"        => "500MB",
                            "franquiaDeVoz"        => "ILIMITADO",
                            "franquiaDeDados"      => "2GB",
                            "formasDePagamento"    => ["DEBITO_AUTOMATICO", "BOLETO"],
                            "portabilidade"        => true,
                            "fidelizacao"          => true,
                            "valorThab"            => 2500
                        ]
                    ]
                ]
            ]
        ];
        $saleService   = \Mockery::mock(SaleService::class)->makePartial();
        $response      = \Mockery::mock(Responseable::class)->makePartial();
        $responsePlans = \Mockery::mock(Responseable::class)->makePartial();
        $connection    = \Mockery::mock(NextelBRConnection::class)->makePartial();
        $assistance    = \Mockery::mock(NextelBRControleCartaoAssistance::class)->makePartial();

        $response->shouldReceive('toArray')->andReturn([
            "protocolo"      => "201700360970534",
            "numeroPedido"   => "1800158134",
            "score"          => "1.000,12",
            "scoreDescricao" => "I"
        ]);
        $connection->shouldReceive('eligibility')->andReturn($response);

        $responsePlans->shouldReceive('toArray')->andReturn($planos);
        $connection->shouldReceive('getPlans')->andReturn($responsePlans);

        $service = new NextelBRService($connection, $saleService, $assistance);
        $user    = $this->userNextel();

        $result = $service->eligibility(['areaCode' => 11, 'mode' => Modes::PORTABILITY], $user);
        self::assertCount(1, $result);
        self::assertEquals(39.99, $result->first()['price']);
    }

    /** @test */
    public function should_return_portability_true_filtered_plans_with_cents_format()
    {
        $planos        = [
            "planos" => [
                [
                    "idPlano"   => "2250|1532440925230",
                    "idOferta"  => "2250|4331|1532440925230",
                    "descricao" => "Controle Ilimitado 2GB + 500MB Bonus GV",
                    "nomePlano" => "CTR 2GB+ilimitado 194/PoS/SMP",
                    "tabelas"   => [
                        [
                            "idTabela"             => "4331",
                            "nomeTabela"           => "Controle Ilimitado 2GB + 500MB Bonus GV",
                            "valorTotal"           => 5900,
                            "valorComDesconto"     => 3999,
                            "periodoFidelizacao"   => 12,
                            "periodoDescontoPlano" => 12,
                            "periodoDescontoBonus" => 12,
                            "bonusInternet"        => "500MB",
                            "franquiaDeVoz"        => "ILIMITADO",
                            "franquiaDeDados"      => "2GB",
                            "formasDePagamento"    => ["DEBITO_AUTOMATICO", "BOLETO"],
                            "portabilidade"        => true,
                            "fidelizacao"          => true,
                            "valorThab"            => 2500
                        ]
                    ]
                ]
            ]
        ];
        $saleService   = \Mockery::mock(SaleService::class)->makePartial();
        $response      = \Mockery::mock(Responseable::class)->makePartial();
        $responsePlans = \Mockery::mock(Responseable::class)->makePartial();
        $connection    = \Mockery::mock(NextelBRConnection::class)->makePartial();
        $assistance    = \Mockery::mock(NextelBRControleCartaoAssistance::class)->makePartial();

        $response->shouldReceive('toArray')->andReturn([
            "protocolo"      => "201700360970534",
            "numeroPedido"   => "1800158134",
            "score"          => "1.000,12",
            "scoreDescricao" => "I"
        ]);
        $connection->shouldReceive('eligibility')->andReturn($response);

        $responsePlans->shouldReceive('toArray')->andReturn($planos);
        $connection->shouldReceive('getPlans')->andReturn($responsePlans);

        $service = new NextelBRService($connection, $saleService, $assistance);
        $user    = $this->userNextel();

        $result = $service->eligibility(['areaCode' => 11, 'mode' => Modes::PORTABILITY], $user);
        self::assertCount(1, $result);
    }


    /** @test */
    public function should_return_none_boleto_filtered_plans_with_cents_format()
    {
        $planos        = [
            "planos" => [
                [
                    "idPlano"   => "2250|1532440925230",
                    "idOferta"  => "2250|4331|1532440925230",
                    "descricao" => "Controle Ilimitado 2GB + 500MB Bonus GV",
                    "nomePlano" => "CTR 2GB+ilimitado 194/PoS/SMP",
                    "tabelas"   => [
                        [
                            "idTabela"             => "4331",
                            "nomeTabela"           => "Controle Ilimitado 2GB + 500MB Bonus GV",
                            "valorTotal"           => 5900,
                            "valorComDesconto"     => 3999,
                            "periodoFidelizacao"   => 12,
                            "periodoDescontoPlano" => 12,
                            "periodoDescontoBonus" => 12,
                            "bonusInternet"        => "500MB",
                            "franquiaDeVoz"        => "ILIMITADO",
                            "franquiaDeDados"      => "2GB",
                            "formasDePagamento"    => ["DEBITO_AUTOMATICO", "BOLETO"],
                            "portabilidade"        => true,
                            "fidelizacao"          => true,
                            "valorThab"            => 2500
                        ]
                    ]
                ]
            ]
        ];
        $saleService   = \Mockery::mock(SaleService::class)->makePartial();
        $response      = \Mockery::mock(Responseable::class)->makePartial();
        $responsePlans = \Mockery::mock(Responseable::class)->makePartial();
        $connection    = \Mockery::mock(NextelBRConnection::class)->makePartial();
        $assistance    = \Mockery::mock(NextelBRControleCartaoAssistance::class)->makePartial();

        $response->shouldReceive('toArray')->andReturn([
            "protocolo"      => "201700360970534",
            "numeroPedido"   => "1800158134",
            "score"          => "1.000,12",
            "scoreDescricao" => "I"
        ]);
        $connection->shouldReceive('eligibility')->andReturn($response);

        $responsePlans->shouldReceive('toArray')->andReturn($planos);
        $connection->shouldReceive('getPlans')->andReturn($responsePlans);

        $service = new NextelBRService($connection, $saleService, $assistance);
        $user    = $this->userNextel();

        $result = $service->eligibility(['areaCode' => 11, 'operation' => Operations::NEXTEL_CONTROLE_CARTAO], $user);
        self::assertCount(0, $result);
    }

    /** @test */
    public function should_return_one_boleto_filtered_plans_with_cents_format()
    {
        $planos        = [
            "planos" => [
                [
                    "idPlano"   => "2250|1532440925230",
                    "idOferta"  => "2250|4331|1532440925230",
                    "descricao" => "Controle Ilimitado 2GB + 500MB Bonus GV",
                    "nomePlano" => "CTR 2GB+ilimitado 194/PoS/SMP",
                    "tabelas"   => [
                        [
                            "idTabela"             => "4331",
                            "nomeTabela"           => "Controle Ilimitado 2GB + 500MB Bonus GV",
                            "valorTotal"           => 5900,
                            "valorComDesconto"     => 3999,
                            "periodoFidelizacao"   => 12,
                            "periodoDescontoPlano" => 12,
                            "periodoDescontoBonus" => 12,
                            "bonusInternet"        => "500MB",
                            "franquiaDeVoz"        => "ILIMITADO",
                            "franquiaDeDados"      => "2GB",
                            "formasDePagamento"    => ["DEBITO_AUTOMATICO", "CARTAO_DE_CREDITO"],
                            "portabilidade"        => false,
                            "fidelizacao"          => true,
                            "valorThab"            => 2500
                        ]
                    ]
                ]
            ]
        ];
        $saleService   = \Mockery::mock(SaleService::class)->makePartial();
        $response      = \Mockery::mock(Responseable::class)->makePartial();
        $responsePlans = \Mockery::mock(Responseable::class)->makePartial();
        $connection    = \Mockery::mock(NextelBRConnection::class)->makePartial();
        $assistance    = \Mockery::mock(NextelBRControleCartaoAssistance::class)->makePartial();

        $response->shouldReceive('toArray')->andReturn([
            "protocolo"      => "201700360970534",
            "numeroPedido"   => "1800158134",
            "score"          => "1.000,12",
            "scoreDescricao" => "I"
        ]);
        $connection->shouldReceive('eligibility')->andReturn($response);

        $responsePlans->shouldReceive('toArray')->andReturn($planos);
        $connection->shouldReceive('getPlans')->andReturn($responsePlans);

        $service = new NextelBRService($connection, $saleService, $assistance);
        $user    = $this->userNextel();

        $result = $service->eligibility(['areaCode' => 11, 'operation' => Operations::NEXTEL_CONTROLE_CARTAO], $user);
        self::assertCount(1, $result);
    }

    /** @test */
    public function should_return_nextel_domains_portability_dates()
    {
        $nextelService = resolve(NextelBRService::class);
        $result        = $nextelService->domains();
        self::assertArrayHasKey('portabilityDates', $result);
    }

    /** @test */
    public function should_return_nextel_domains_banks()
    {
        $nextelService = resolve(NextelBRService::class);
        $result        = $nextelService->domains();
        self::assertArrayHasKey('banks', $result);
    }

    /** @test */
    public function should_return_nextel_domains_fromOperators()
    {
        $nextelService = resolve(NextelBRService::class);
        $result        = $nextelService->domains();
        self::assertArrayHasKey('fromOperators', $result);
        self::assertNotEmpty($result['fromOperators']);
    }

    /** @test */
    public function should_return_nextel_domains_dueDates()
    {
        $nextelService = resolve(NextelBRService::class);
        $result        = $nextelService->domains();
        self::assertArrayHasKey('dueDates', $result);
        self::assertNotEmpty($result['dueDates']);
    }

    /** @test */
    public function should_return_message_when_m4u_modal()
    {
        $transaction = ServiceTransactionGenerator::generate();
        $service     = $this->factory()->of(NextelBRControleCartao::class)->make([
            'serviceTrnsaction' => $transaction . '-0'
        ]);
        $pointOfSale = $this->pointOfSaleNextel()->toArray();
        factory(Sale::class)->create([
            'saleTransaction' => $transaction,
            'pointOfSale'     => $pointOfSale,
            'services'        => [$service]
        ]);
        $nextelService = resolve(NextelBRControleCartaoAssistance::class);
        $result        = $nextelService->integrateService($service, ['executed' => true]);

        self::assertArrayHasKey('message', $result->getAdapted());
    }


    /** @test */
    public function should_return_link_when_m4u_modal()
    {
        $transaction   = ServiceTransactionGenerator::generate();
        $service       = $this->factory()->of(NextelBRControleCartao::class)->make([
            'serviceTrnsaction' => $transaction . '-0'
        ]);
        $pointOfSale   = $this->pointOfSaleNextel()->toArray();
        $sale          = factory(Sale::class)->create([
            'saleTransaction' => $transaction,
            'pointOfSale'     => $pointOfSale,
            'services'        => [$service]
        ]);
        $nextelService = resolve(NextelBRControleCartaoAssistance::class);
        $result        = $nextelService->integrateService($service, ['action' => 'aaa']);

        self::assertArrayHasKey('link', $result->getAdapted());
    }

    /** @test */
    public function domains()
    {
    }
}
