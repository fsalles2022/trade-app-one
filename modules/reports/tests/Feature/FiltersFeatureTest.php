<?php
namespace Reports\Tests\Feature;

use Illuminate\Http\Response;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class FiltersFeatureTest extends TestCase
{
    use AuthHelper, ElasticSearchHelper;

    protected $endpoint = '/reports/filters';

    /** @test */
    public function get_should_response_with_status_200(): void
    {
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('GET', '/' . $this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_response_with_contextualized_filter(): void
    {
        $network     = (new NetworkBuilder())->build();
        $userHelper  = (new UserBuilder())->withNetwork($network)->build();
        $hierarchy   = (new HierarchyBuilder())->withNetwork($network)->withUser($userHelper)->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->withUser($userHelper)->withHierarchy($hierarchy)->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('GET', '/' . $this->endpoint);

        $responseExpected = [
            [
                'id' => $network->slug,
                'label' => $network->label,
                'hierarchies' => [
                    [
                        'id' => $hierarchy->slug,
                        'label' => $hierarchy->label,
                        'pointsOfSale' => [
                            [
                                'id' => $pointOfSale->cnpj,
                                'label' => $pointOfSale->label
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response->assertJson($responseExpected);
    }
}
