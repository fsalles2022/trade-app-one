<?php


namespace Recommendation\Unit;

use Recommendation\Models\Recommendation;
use Recommendation\Services\RecommendationService;
use Recommendation\tests\RecommendationTestBook;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Tests\TestCase;

class RecommendationServiceTest extends TestCase
{
    protected $recommendationService;
    protected $recommendation;

    protected function setUp()
    {
        parent::setUp();
        $this->recommendationService = resolve(RecommendationService::class);
        $this->setupInitialData();
    }

    /** @test */
    public function shouldInsertRecommendation(): void
    {
        $this->assertDatabaseHas(
            'recommendations',
            ['registration' => RecommendationTestBook::SUCCESS_REGISTRATION]
        );
    }

    /** @test */
    public function shouldUpdateRecommendation(): void
    {
        $personData = RecommendationTestBook::VALID_RECOMMENDATION;
        unset($personData['pointOfSaleId']);
        data_set($personData, 'statusCode', 'INACTIVE');
        data_set($personData, 'registration', '999999');

        $this->recommendationService->updateRecommendation($this->recommendation, $personData);

        $this->assertDatabaseHas(
            'recommendations',
            ['statusCode' => 'INACTIVE', 'registration' => '999999']
        );
    }

    /** @test */
    public function shouldReturnValidRecommendationRegistry(): void
    {
        $recommendation = $this->recommendationService
            ->getRecommendationByRegistration(RecommendationTestBook::SUCCESS_REGISTRATION);

        $this->assertNotEmpty($recommendation);
        $this->assertInstanceOf(Recommendation::class, $recommendation);
    }

    /** @test */
    public function shouldReturnInvalidRecommendationRegistry(): void
    {
        $recommendation = $this->recommendationService
            ->getRecommendationByRegistration(RecommendationTestBook::INVALID_REGISTRATION);

        $this->assertEmpty($recommendation);
    }

    private function setupInitialData(): void
    {
        $network     = factory(Network::class)->create();
        $pointOfSale = factory(PointOfSale::class)->create([
            'networkId' => $network->id
        ]);

        $personData = RecommendationTestBook::VALID_RECOMMENDATION;
        data_set($personData, 'pointOfSaleId', $pointOfSale->id);

        $this->recommendation = $this->recommendationService
            ->createRecommendation($personData);
    }
}
