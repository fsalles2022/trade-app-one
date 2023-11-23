<?php

namespace Reports\Tests\Feature;

use Illuminate\Http\Response;
use Reports\Tests\Fixture\ElasticSearchServicePieChartFixture;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;

class TotalSalesByVivoOperatorFeatureTest extends TestCase
{
    use AuthHelper, ElasticSearchHelper, ArrayAssertTrait;

    protected $endpoint = '/reports/pie/total-sales-by-vivo';

    /** @test */
    public function should_return_response_with_status_200_and_a_valid_structure()
    {
        $elasticFixture = ElasticSearchServicePieChartFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertArrayStructure($response->json(), [['name','y', 'sum_price']]);
    }
}
