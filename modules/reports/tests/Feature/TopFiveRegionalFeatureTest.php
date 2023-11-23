<?php

namespace Reports\Tests\Feature;

use Illuminate\Http\Response;
use Reports\Tests\Fixture\ElasticSearchTopFiveRegionalFixture;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;

class TopFiveRegionalFeatureTest extends TestCase
{
    use AuthHelper, ElasticSearchHelper, ArrayAssertTrait;

    protected $endpoint = '/reports/column/top-5-regional';

    /** @test */
    public function get_should_response_with_status_200_and_a_correct_structure()
    {
        $elasticFixture = ElasticSearchTopFiveRegionalFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);
        $userHelper = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->post($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertArrayStructure($response->json(), [
            'title',
            'hierarchies' => [],
            'data' => [[
                'color',
                'name',
                'data' => []
            ]],
        ]);
    }
}
