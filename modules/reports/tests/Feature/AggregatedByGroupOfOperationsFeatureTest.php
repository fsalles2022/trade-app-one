<?php

namespace Reports\Tests\Feature;

use Illuminate\Http\Response;
use Reports\Exceptions\ReportExceptions;
use Reports\Tests\Fixture\SalesByAggregatedOperationsFixture;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class AggregatedByGroupOfOperationsFeatureTest extends TestCase
{
    use AuthHelper, ElasticSearchHelper;

    protected $endpoint = '/reports/aggregated/by-group-of-operations';

    /** @test */
    public function get_should_response_with_status_200_and_a_valid_structure()
    {
        $elasticFixture = SalesByAggregatedOperationsFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->post($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            '*' => [
                "cnpj",
                "total",
                'groups' => [
                    'PRE',
                    'CONTROLE',
                    'POS',
                ]
            ]
        ]);
    }

//    /** @test */
    public function get_should_response_with_status_400_when_can_connect_to_elastic_search()
    {
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->post($this->endpoint);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJsonFragment([
            'message' => trans('exceptions.' . ReportExceptions::FAILED_REPORT_BUILD)
        ]);
    }
}
