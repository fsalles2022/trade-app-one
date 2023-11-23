<?php

namespace Reports\Tests\Feature;

use Illuminate\Http\Response;
use TradeAppOne\Tests\TestCase;
use TradeAppOne\Tests\Helpers\AuthHelper;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use Reports\Tests\Fixture\ElasticSearchTotalSalesTriangulationFixture;

class TotalSalesTriangulationServiceTest extends TestCase
{
    use AuthHelper, ElasticSearchHelper;

    protected $endpoint = '/reports/number/group-of-telecommunication-triangulations-total';

    /** @test */
    public function should_return_response_with_200()
    {
        $elasticFixture = ElasticSearchTotalSalesTriangulationFixture::fixture();

        $this->mockElasticSearchConnection($elasticFixture);
        $userHelper = (new UserBuilder())->build();

        $response = $this->withHeader('Authorization', $this->loginUser($userHelper))
            ->post($this->endpoint);

        $response->assertJsonStructure([
            'withTriangulation'    => ['name','quantity','price'],
            'withoutTriangulation' => ['name','quantity','price'],
            'total'                => ['quantity','price']
        ]);
        $response->assertStatus(Response::HTTP_OK);
    }
}
