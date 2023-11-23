<?php

namespace Reports\Tests\Feature;

use Illuminate\Http\Response;
use TradeAppOne\Tests\TestCase;
use TradeAppOne\Tests\Helpers\AuthHelper;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use Reports\Tests\Fixture\ElasticSearchSalesWithDeviceFixture;

class TotalSalesWithDeviceFeatureTest extends TestCase
{
    use ElasticSearchHelper, AuthHelper;
  
    protected $endpoint = 'reports/donuts/total-sales-with-device';

    /** @test */
    public function post_should_response_with_status_200()
    {
        $elasticFixture = ElasticSearchSalesWithDeviceFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);
        $user = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function post_should_response_with_a_valid_list()
    {
        $elasticFixture = ElasticSearchSalesWithDeviceFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);
        $user = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('POST', $this->endpoint);

        $response->assertJsonStructure(['operators' => ['*' => ['key', 'doc_count', 'operation' => ['buckets' => ['*' => ['key', 'doc_count', 'sum_price' => ['value']]]]]]]);
    }
}
