<?php

namespace Reports\Tests\Feature;

use Illuminate\Http\Response;
use Reports\Tests\Fixture\ElasticSearchMonthGroupOfSales;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class DailySalesByGroupOfOperationsControllerTest extends TestCase
{
    use AuthHelper, ElasticSearchHelper;
    protected $endpoint = 'reports/number/group-of-telecommunication-operations-by-daily';

    /** @test */
    public function get_should_response_with_status_200()
    {
        $elasticFixture = ElasticSearchMonthGroupOfSales::fixture();
        $this->mockElasticSearchConnection($elasticFixture);
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->json('POST', '/' . $this->endpoint);
        $response->assertStatus(Response::HTTP_OK);
    }
}
