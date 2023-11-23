<?php

namespace Integrators\tests\Feature;

use Authorization\Http\Middleware\ThirdPartiesMiddleware;
use Authorization\Services\ThirdPartyAccessDatabase;
use Authorization\tests\Helpers\Builders\ThirdPartyDatabaseBuilder;
use Illuminate\Http\Response;
use Integrators\tests\Fixtures\SaleFromSiv;
use Mockery;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ResidentialImportTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setupAccesskey();
        $this->setupPreConditionsToImport();
    }

    /** @test */
    public function should_return_202_when_sale_saved(): void
    {
        $payload = SaleFromSiv::residentialSale();
        $this->withHeader(ThirdPartiesMiddleware::ACCESS_KEY, 'ACCESS_KEY')
            ->json('POST', '/integrators/residential', $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['body', 'statusCode']);
    }

    /** @test */
    public function should_return_422_when_sale_empty(): void
    {
        $this->withHeader(ThirdPartiesMiddleware::ACCESS_KEY, 'ACCESS_KEY')
            ->json('POST', '/integrators/residential', [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['errors']);
    }

    /** @test */
    public function should_return_401_when_sale_empty(): void
    {
        $this->json('POST', '/integrators/residential', [])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    private function setupAccesskey(): void
    {
        $user             = (new UserBuilder())->build();
        $thirdPartyConfig = (new ThirdPartyDatabaseBuilder())
            ->withAccessKey("ACCESS_KEY")
            ->withAccessUser($user)
            ->withRoutes(collect([['uri' => 'integrators/residential', 'method' => 'POST']]))
            ->build();
        app()->bind(ThirdPartyAccessDatabase::class, function () use ($thirdPartyConfig) {
            $thirdPartyAccessConfigMock = Mockery::mock(ThirdPartyAccessDatabase::class);
            $thirdPartyAccessConfigMock->shouldReceive('getByAccessKey')
                ->with('ACCESS_KEY')
                ->andReturn($thirdPartyConfig);
            return $thirdPartyAccessConfigMock;
        });
    }

    private function setupPreConditionsToImport(): void
    {
        $user = (new UserBuilder())->withCustomParameters([
            'cpf' => '01296802140'
        ])->build();

        (new PointOfSaleBuilder())
            ->withState('with_identifiers')
            ->withUser($user)
            ->build();
    }
}
