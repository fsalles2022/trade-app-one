<?php

namespace Reports\Tests\Feature;

use Illuminate\Http\Response;
use Reports\Tests\Fixture\ElasticSearchTopPointOfSaleFixture;
use Reports\Tests\Helpers\ElasticSearchHelper;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class TopPointOfSalesByOperatorControllerTest extends TestCase
{
    use AuthHelper, ElasticSearchHelper;

    protected $endpoint = '/reports/lines/tops-points-of-sales';

    /** @test */
    public function get_should_response_with_status_200_123()
    {
        $elasticFixture = ElasticSearchTopPointOfSaleFixture::getSaleArray();
        $this->mockElasticSearchConnection($elasticFixture);
        $userHelper = (new UserBuilder())->build();

        $pointOfSale = factory(PointOfSale::class)->make(['cnpj' => '33200056002001']);
        $network     = (new NetworkBuilder())->build();
        $pointOfSale->network()->associate($network)->save();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($userHelper))
            ->post($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
    }
}
