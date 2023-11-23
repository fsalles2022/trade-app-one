<?php

namespace ClaroBR\Tests\Unit\Domain\Services;

use ClaroBR\Connection\SivConnection;
use ClaroBR\Services\ClaroBRFillDependents;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Exceptions\BusinessExceptions\ProductNotFoundException;
use TradeAppOne\Tests\TestCase;

class ClaroBRFillDependentsTest extends TestCase
{
    const PROMOTION_ID    = 46;
    const PRODUCT_ID      = 59;
    const AREA_CODE       = 11;
    const PRODUCT_PRICE   = 39.99;
    const PROMOTION_PRICE = -10.00;

    /** @test */
    public function should_fill_with_dependent_promotion_label()
    {
        $originalDependents = [
            [
                "mode"      => "NOVO",
                "type"      => "CONTROLE",
                "product"   => self::PRODUCT_ID,
                "promotion" => self::PROMOTION_ID,
                "iccid"     => "89550539150006195631"
            ]
        ];
        $response           = \Mockery::mock(RestResponse::class)->makePartial();
        $response->shouldReceive('toArray')->andReturn(self::returnOfPlans());
        $connection = \Mockery::mock(SivConnection::class)->makePartial();
        $connection->shouldReceive('getPlans')->once()->andReturn($response);
        $claroDependents = new ClaroBRFillDependents($connection);
        $result          = $claroDependents->fill('123', $originalDependents, self::AREA_CODE);
        self::assertArrayHasKey('label', $result[0]['promotion']);
    }

    /** @test */
    public function should_fill_with_dependent_promotion_price()
    {
        $originalDependents = [
            [
                "mode"      => "NOVO",
                "type"      => "CONTROLE",
                "product"   => self::PRODUCT_ID,
                "promotion" => self::PROMOTION_ID,
                "iccid"     => "89550539150006195631"
            ]
        ];
        $response           = \Mockery::mock(RestResponse::class)->makePartial();
        $response->shouldReceive('toArray')->andReturn(self::returnOfPlans());
        $connection = \Mockery::mock(SivConnection::class)->makePartial();
        $connection->shouldReceive('getPlans')->once()->andReturn($response);
        $claroDependents = new ClaroBRFillDependents($connection);
        $result          = $claroDependents->fill('123', $originalDependents, self::AREA_CODE);
        self::assertArrayHasKey('price', $result[0]['promotion']);
        self::assertTrue(is_float($result[0]['promotion']['price']));
    }

    /** @test */
    public function should_fill_with_dependent_product_price()
    {
        $originalDependents = [
            [
                "mode"      => "NOVO",
                "type"      => "CONTROLE",
                "product"   => self::PRODUCT_ID,
                "promotion" => self::PROMOTION_ID,
                "iccid"     => "89550539150006195631"
            ]
        ];
        $response           = \Mockery::mock(RestResponse::class)->makePartial();
        $response->shouldReceive('toArray')->andReturn(self::returnOfPlans());
        $connection = \Mockery::mock(SivConnection::class)->makePartial();
        $connection->shouldReceive('getPlans')->once()->andReturn($response);
        $claroDependents = new ClaroBRFillDependents($connection);
        $result          = $claroDependents->fill('123', $originalDependents, self::AREA_CODE);
        self::assertArrayHasKey('price', $result[0]);
        self::assertTrue(is_float($result[0]['price']));
    }

    /** @test */
    public function should_fill_with_dependent_promotion_price_applied_in_product_price()
    {
        $originalDependents = [
            [
                "mode"      => "NOVO",
                "type"      => "CONTROLE",
                "product"   => self::PRODUCT_ID,
                "promotion" => self::PROMOTION_ID,
                "iccid"     => "89550539150006195631"
            ]
        ];
        $response           = \Mockery::mock(RestResponse::class)->makePartial();
        $response->shouldReceive('toArray')->andReturn(self::returnOfPlans());
        $connection = \Mockery::mock(SivConnection::class)->makePartial();
        $connection->shouldReceive('getPlans')->once()->andReturn($response);
        $claroDependents = new ClaroBRFillDependents($connection);
        $result          = $claroDependents->fill('123', $originalDependents, self::AREA_CODE);
        self::assertEquals(self::PRODUCT_PRICE + self::PROMOTION_PRICE, $result[0]['price']);
    }

    /** @test */
    public function should_throw_exception_when_product_not_found()
    {
        $originalDependents = [
            [
                "mode"      => "NOVO",
                "type"      => "CONTROLE",
                "product"   => 22,
                "promotion" => 99,
                "iccid"     => "89550539150006195631"
            ]
        ];
        $response           = \Mockery::mock(RestResponse::class)->makePartial();
        $response->shouldReceive('toArray')->andReturn(self::returnOfPlans());
        $connection = \Mockery::mock(SivConnection::class)->makePartial();
        $connection->shouldReceive('getPlans')->once()->andReturn($response);
        $claroDependents = new ClaroBRFillDependents($connection);
        $this->expectException(ProductNotFoundException::class);
        $claroDependents->fill('123', $originalDependents, self::AREA_CODE);
    }

    private static function returnOfPlans()
    {
        return [
            "type"    => "success",
            "message" => "",
            "data"    => [
                "current_page" => 1,
                "data"         => [
                    [
                        "id"               => self::PRODUCT_ID,
                        "nome"             => "DEPENDENTE_PLAY_COM_COMP._TOTAL",
                        "label"            => "Dependente play com comp. Total",
                        "codigo_operadora" => "414384645",
                        "ativo"            => 1,
                        "descricao"        => "Dependente play com comp. Total",
                        "created_at"       => "2018-09-29 21:00:17",
                        "updated_at"       => "2018-11-21 14:16:30",
                        "pontuacao"        => 0,
                        "faturas"          => [
                            "EMAIL"             => "Email",
                            "VIA_POSTAL"        => "Via Postal",
                            "DEBITO_AUTOMATICO" => "Débito Automático",
                        ],
                        "plan_type"        => [
                            "id"    => 6,
                            "label" => "Voz + Dados",
                            "nome"  => "VOZ_DADOS",
                            "ativo" => 1,
                        ],
                        "plans_area_code"  => [
                            [
                                "id"         => 15165,
                                "ddd"        => self::AREA_CODE,
                                "plano_id"   => self::PRODUCT_ID,
                                "valor"      => self::PRODUCT_PRICE,
                                "ativo"      => 1,
                                "created_at" => "2018-11-21 14:16:30",
                                "updated_at" => "2018-11-21 14:16:30",
                                "promotions" => [
                                    [
                                        "id"              => self::PROMOTION_ID,
                                        "nome"            => "Dependente play com comp. Total",
                                        "ativo"           => 1,
                                        "valor"           => self::PROMOTION_PRICE,
                                        "created_at"      => "2018-11-21 14:00:00",
                                        "updated_at"      => "2018-11-21 14:00:00",
                                        "categoria"       => "ATIVACAO",
                                        "plano_tipo_id"   => 6,
                                        "requer_aparelho" => 0,
                                        "fidelidade"      => 0,
                                        "multa"           => "0.00",
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
