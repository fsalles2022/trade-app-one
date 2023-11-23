<?php

namespace Reports\Tests\Feature;

use Illuminate\Http\Response;
use Reports\Tests\Fixture\ElasticSearchStatusMultiOperatorsFixture;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class TotalSalesFeatureTest extends TestCase
{
    use AuthHelper, ElasticSearchHelper;
  
    protected $endpoint = 'reports/number/total-sales';

    /** @test */
    public function get_should_response_with_status_200()
    {
        $elasticFixture = ElasticSearchStatusMultiOperatorsFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpoint);
        
        $response->assertStatus(Response::HTTP_OK);
    }
}
