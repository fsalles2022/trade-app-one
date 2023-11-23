<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Services;

use GuzzleHttp\Psr7\Response;
use TradeAppOne\Tests\TestCase;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Http\Response as HttpResponse;
use Mockery;
use Mockery\MockInterface;
use SurfPernambucanas\Adapters\PagtelResponseAdapter;
use SurfPernambucanas\Assistances\SurfPernambucanasPreAssistance;
use SurfPernambucanas\Models\SurfPernambucanasPrePago;
use SurfPernambucanas\Services\SurfPernambucanasSaleAssistance;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class SurfPernambucanasSaleAssistanceTest extends TestCase
{
    /** @var Factory */
    protected $factory;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = Factory::construct(\Faker\Factory::create(), base_path('modules/surfpernambucanas/tests/Factories'));
    }
    
    /** @return array[] */
    public function provider_operation_with_assistance_for_tests(): array
    {
        return [
            [
                SurfPernambucanasPrePago::class,
                SurfPernambucanasPreAssistance::class,
            ],
        ];
    }

    /** @dataProvider provider_operation_with_assistance_for_tests */
    public function test_integration_operation_assistance_sale(string $operationClass, string $assistanceClass): void
    {
        $service = $this->factory->of($operationClass)->make();

        $this->mockOperationServiceAssistance($assistanceClass);

        $saleAssistance = $this->resolveSaleAssistance();

        $responseAdapter = $saleAssistance->integrateService($service, []);

        $this->assertInstanceOf(PagtelResponseAdapter::class, $responseAdapter);
    }

    private function resolveSaleAssistance(): SurfPernambucanasSaleAssistance
    {
        return resolve(SurfPernambucanasSaleAssistance::class);
    }

    private function mockOperationServiceAssistance(string $class): void
    {
        $responseMock = new Response(
            HttpResponse::HTTP_OK,
            [],
            ''
        );

        $adapter = new PagtelResponseAdapter(RestResponse::success($responseMock));
        
        $this->instance(
            $class,
            Mockery::mock($class, function (MockInterface $mock) use ($adapter): void {
                $mock->shouldReceive('integrateService')
                    ->andReturn($adapter);
            })
        );
    }
}
