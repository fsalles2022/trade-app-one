<?php

namespace Recommendation\tests\Feature;

use Illuminate\Http\Response as HttpResponse;
use Recommendation\Services\RecommendationService;
use Recommendation\tests\RecommendationTestBook;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class RecommendationFeatureTest extends TestCase
{
    use AuthHelper;

    protected const URI = '/recommendations';
    protected $userAuth;

    /** @test */
    public function should_return_valid_indicated_by_registration(): void
    {
        $this->makeRecommendation();
        $uri      = self::URI . '?registration=' . RecommendationTestBook::SUCCESS_REGISTRATION;
        $response = $this->authAs($this->userAuth)->get($uri);

        $data = $response->json();
        $this->assertNotEmpty(data_get($data, 'registration'));
        $this->assertNotEmpty(data_get($data, 'name'));
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
    }

    /** @test */
    public function should_return_invalid_indicated_by_registration(): void
    {
        $this->makeRecommendation();
        $uri      = self::URI . '?registration=' . RecommendationTestBook::INVALID_REGISTRATION;
        $response = $this->authAs($this->userAuth)->get($uri);

        $data = $response->json();
        $this->assertEmpty($data);
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
    }

    /** @test */
    public function should_return_invalid_indicated_by_valid_registration_different_pointofsale(): void
    {
        $this->makeRecommendation();
        $uri      = self::URI . '?registration=' . RecommendationTestBook::INVALID_REGISTRATION;
        $response = $this->authAs()->get($uri);

        $data = $response->json();
        $this->assertEmpty($data);
        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
    }

    private function makeRecommendation(): void
    {
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $personData  = RecommendationTestBook::VALID_RECOMMENDATION;

        $personData['pointOfSaleId'] = $pointOfSale->id;

        resolve(RecommendationService::class)
            ->createRecommendation($personData);

        $this->userAuth = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
    }
}
