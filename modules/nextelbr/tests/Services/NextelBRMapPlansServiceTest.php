<?php

namespace NextelBR\Tests\Services;

use NextelBR\Enumerators\NextelInvoiceTypes;
use NextelBR\Services\NextelBRMapPlansService;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Tests\TestCase;

class NextelBRMapPlansServiceTest extends TestCase
{
    protected $planos = [
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
                    "formasDePagamento"    => [NextelInvoiceTypes::CARTAO_DE_CREDITO],
                    "portabilidade"        => false,
                    "fidelizacao"          => true,
                    "valorThab"            => 2500
                ]
            ]
        ],
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
                    "formasDePagamento"    => [NextelInvoiceTypes::BOLETO],
                    "portabilidade"        => false,
                    "fidelizacao"          => true,
                    "valorThab"            => 2500
                ]
            ]
        ],
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
                    "formasDePagamento"    => [NextelInvoiceTypes::DEBITO_AUTOMATICO_LIST, NextelInvoiceTypes::BOLETO],
                    "portabilidade"        => true,
                    "fidelizacao"          => true,
                    "valorThab"            => 2500
                ]
            ]
        ],
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
                    "formasDePagamento"    => [NextelInvoiceTypes::DEBITO_AUTOMATICO_LIST, NextelInvoiceTypes::BOLETO],
                    "portabilidade"        => true,
                    "fidelizacao"          => true,
                    "valorThab"            => 2500
                ]
            ]
        ]
    ];

    protected $withFee = [
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
                    "formasDePagamento"    => [NextelInvoiceTypes::CARTAO_DE_CREDITO],
                    "portabilidade"        => false,
                    "fidelizacao"          => true,
                    "valorThab"            => 2500,
                    "flatFee"              => true,
                    "periodoFlatFee"       => 6
                ]
            ]
        ],
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
                    "formasDePagamento"    => [NextelInvoiceTypes::BOLETO],
                    "portabilidade"        => false,
                    "fidelizacao"          => true,
                    "valorThab"            => 2500,
                    "flatFee"              => false,
                    "periodoFlatFee"       => 6
                ]
            ]
        ]
    ];

    /** @test */
    public function should_return_all()
    {
        $filtered = NextelBRMapPlansService::map($this->planos);
        self::assertCount(4, $filtered);
    }

    /** @test */
    public function should_return_controle_boleto()
    {
        $filtered = NextelBRMapPlansService::map(
            $this->planos,
            ['operation' => Operations::NEXTEL_CONTROLE_BOLETO]
        );

        self::assertCount(3, $filtered);
        self::assertEquals(Operations::NEXTEL_CONTROLE_BOLETO, $filtered->first()['operation']);
    }

    /** @test */
    public function should_return_controle_cartao()
    {
        $filtered = NextelBRMapPlansService::map(
            $this->planos,
            ['mode' => Modes::ACTIVATION, 'operation' => Operations::NEXTEL_CONTROLE_CARTAO]
        );
        self::assertCount(1, $filtered);
        self::assertEquals(Operations::NEXTEL_CONTROLE_CARTAO, $filtered->first()['operation']);
    }


    /** @test */
    public function should_return_fee()
    {
        $filtered = NextelBRMapPlansService::map(
            $this->withFee,
            ['mode' => Modes::ACTIVATION, 'operation' => Operations::NEXTEL_CONTROLE_CARTAO]
        );
        self::assertCount(1, $filtered);
        self::assertEquals('GANHE MAIS GIGA a cada 6 meses!!! O melhor é que o valor da sua conta não muda por 2 anos! ', $filtered->first()['details'][3]);
    }
}
