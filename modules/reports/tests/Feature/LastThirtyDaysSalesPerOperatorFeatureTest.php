<?php

namespace Reports\Tests\Feature;

use Illuminate\Http\Response;
use Reports\Tests\Fixture\ElasticSearchMonthlySalesAmountFixture;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class LastThirtyDaysSalesPerOperatorFeatureTest extends TestCase
{
    use AuthHelper, ElasticSearchHelper;

    protected $endpoint = '/reports/lines/last-thirty-days-sales-per-operator';

    /** @test */
    public function get_should_response_with_status_200()
    {
        $elasticFixture = ElasticSearchMonthlySalesAmountFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
    }
}
