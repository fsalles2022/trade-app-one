<?php

namespace Reports\Tests\Feature;

use Illuminate\Http\Response;
use Reports\Tests\Fixture\ElasticSearchSalesByNetworkPlansTelecommunicationFixture;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class SalesByNetworkPlansTelecommunicationFeatureTest extends TestCase
{
    use AuthHelper, ElasticSearchHelper;

    protected $endpoint = 'reports/column/sales-by-network-telecommunication';

    /** @test */
    public function get_should_response_with_status_200()
    {
        $elasticFixture = ElasticSearchSalesByNetworkPlansTelecommunicationFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->post($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
    }
}
