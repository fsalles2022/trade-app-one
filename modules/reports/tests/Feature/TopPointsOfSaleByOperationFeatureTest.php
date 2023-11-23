<?php

namespace Reports\Tests\Feature;

use TradeAppOne\Tests\TestCase;
use TradeAppOne\Tests\Helpers\AuthHelper;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use Reports\Tests\Fixture\ElasticSearchTopPointsOfSaleByOperationFixture;

class TopPointsOfSaleByOperationFeatureTest extends TestCase
{
    use AuthHelper, ElasticSearchHelper, ArrayAssertTrait;

    protected $endpoint = '/reports/column/top-points-of-sale-by-operation';

    /** @test */
    public function post_should_response_with_status_200_and_correct_structure_sadfs()
    {
        $network = (new NetworkBuilder())->build();

        $pointsOfSale = [
            "45242914013428",
            "45242914006480",
            "45242914006995",
            "33200056002001",
            "33200056006180",
            "33200056001030",
            "33200056021812",
            "45242914016281",
            "33200056022207",
            "61099834063273"
        ];

        $labels = [];

        foreach ($pointsOfSale as $pointOfSale) {
            $label = strval(rand());
            array_push($labels, $label);

            factory(PointOfSale::class)->create([
                'label' => $label,
                'cnpj'  => $pointOfSale,
                'networkId' => $network->id
            ]);
        }

        $elasticFixture = ElasticSearchTopPointsOfSaleByOperationFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->post($this->endpoint);

        $now        = now()->format('d/m/y');
        $startMonth = now()->startOfMonth()->format('d/m/y');

        $title = trans('reports::chartnames.column.top_pdvs_by_operation', [
            'startDate' => $startMonth,
            'endDate'   => $now,
            'top'       => 10
        ]);

        $expect = self::expectResponse($title, $labels);

        $this->assertEquals($expect, $response->json());
    }

    public static function expectResponse($title, $labels)
    {
        return array (
            'title' => $title,
            'pointsOfSales' => $labels,
            'sales' =>
                array (
                    0 =>
                        array (
                            'color' => 'rgba(97, 167, 255, .8)',
                            'name' => 'Pré Pago',
                            'data' =>
                                array (
                                    0 =>
                                        array (
                                            'y' => 6,
                                            'revenues' => "R$ 0,00",
                                        ),
                                    1 =>
                                        array (
                                            'y' => 19,
                                            'revenues' => "R$ 0,00",
                                        ),
                                    2 =>
                                        array (
                                            'y' => 20,
                                            'revenues' => "R$ 0,00",
                                        ),
                                    3 =>
                                        array (
                                            'y' => 26,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    4 =>
                                        array (
                                            'y' => 11,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    5 =>
                                        array (
                                            'y' => 11,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    6 =>
                                        array (
                                            'y' => 19,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    7 =>
                                        array (
                                            'y' => 16,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    8 =>
                                        array (
                                            'y' => 2,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    9 =>
                                        array (
                                            'y' => 33,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                ),
                        ),
                    1 =>
                        array (
                            'color' => 'rgba(20, 90, 255, .8)',
                            'name' => 'Pós Pago',
                            'data' =>
                                array (
                                    0 =>
                                        array (
                                            'y' => 0,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    1 =>
                                        array (
                                            'y' => 0,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    2 =>
                                        array (
                                            'y' => 0,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    3 =>
                                        array (
                                            'y' => 0,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    4 =>
                                        array (
                                            'y' => 0,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    5 =>
                                        array (
                                            'y' => 0,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    6 =>
                                        array (
                                            'y' => 0,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    7 =>
                                        array (
                                            'y' => 0,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    8 =>
                                        array (
                                            'y' => 0,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                    9 =>
                                        array (
                                            'y' => 0,
                                            'revenues' => 'R$ 0,00',
                                        ),
                                ),
                        ),
                    2 =>
                        array (
                            'color' => 'rgba(40, 120, 255, .8)',
                            'name' => 'Controle',
                            'data' =>
                                array (
                                    0 =>
                                        array (
                                            'y' => 120,
                                            'revenues' => 'R$ 4.107,49',
                                        ),
                                    1 =>
                                        array (
                                            'y' => 104,
                                            'revenues' => 'R$ 4.062,94',
                                        ),
                                    2 =>
                                        array (
                                            'y' => 85,
                                            'revenues' => 'R$ 4.201,37',
                                        ),
                                    3 =>
                                        array (
                                            'y' => 71,
                                            'revenues' => 'R$ 3.588,35',
                                        ),
                                    4 =>
                                        array (
                                            'y' => 88,
                                            'revenues' => 'R$ 4.877,70',
                                        ),
                                    5 =>
                                        array (
                                            'y' => 85,
                                            'revenues' => 'R$ 4.513,06',
                                        ),
                                    6 =>
                                        array (
                                            'y' => 59,
                                            'revenues' => 'R$ 2.907,14',
                                        ),
                                    7 =>
                                        array (
                                            'y' => 57,
                                            'revenues' => 'R$ 2.273,06',
                                        ),
                                    8 =>
                                        array (
                                            'y' => 64,
                                            'revenues' => 'R$ 3.573,92',
                                        ),
                                    9 =>
                                        array (
                                            'y' => 31,
                                            'revenues' => 'R$ 948,30',
                                        ),
                                ),
                        ),
                ),
        );
    }
}
