<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Services;

use GuzzleHttp\Psr7\Response;
use TradeAppOne\Tests\TestCase;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Http\Response as HttpResponse;
use Mockery;
use Mockery\MockInterface;
use SurfPernambucanas\Adapters\PagtelActivationPlansResponseAdapter;
use SurfPernambucanas\Adapters\PagtelPlansResponseAdapter;
use SurfPernambucanas\Adapters\PagtelResponseAdapter;
use SurfPernambucanas\Enumerators\PagtelResponseCode;
use SurfPernambucanas\Models\SurfPernambucanasPrePago;
use SurfPernambucanas\Services\MountNewAttributeFromSurf;
use SurfPernambucanas\Services\PagtelService;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class MountNewAttributeFromSurfTest extends TestCase
{
    /** @var Factory */
    protected $factory;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = Factory::construct(\Faker\Factory::create(), base_path('modules/surfpernambucanas/tests/Factories'));
    }

    private function mount_adapter_valid_plans_response(): PagtelPlansResponseAdapter
    {
        $responseMock = new Response(
            HttpResponse::HTTP_OK,
            [],
            json_encode([
                'code'       => PagtelResponseCode::SUCCESS,
                'msg'        => 'Sucesso',
                'valueList'  => [
                    [
                        'value' => '3000',
                        'note'  => 'Plano 30 Coins',
                    ],
                    [
                        'value' => '4000',
                        'note'  => 'Plano 40 Coins',
                    ],
                    [
                        'value' => '2990',
                        'note'  => 'Plano 29,90 Coins',
                    ],
                    [
                        'value' => '3999',
                        'note'  => 'Plano 39,99 Coins',
                    ],
                ],
            ])
        );

        return new PagtelPlansResponseAdapter(RestResponse::success($responseMock));
    }

    /** @return array[] */
    public function provider_plans_to_tests(): array
    {
        return [
            [
                'Plano 30 Coins',
                30.00
            ],
            [
                'Plano 40 Coins',
                40.00
            ],
            [
                'Plano 29,90 Coins',
                29.90
            ],
            [
                'Plano 39,99 Coins',
                39.99
            ],
        ];
    }

    /** @dataProvider provider_plans_to_tests */
    public function test_plan_match(string $product, float $valueExpected): void
    {
        $adapter = $this->mount_adapter_valid_plans_response();

        $this->mockPagtelServiceByResponseAdapter($adapter);

        $mountNewAttribute = resolve(MountNewAttributeFromSurf::class);

        $service = $this->factory->of(SurfPernambucanasPrePago::class)
            ->make([
                'operation' => Operations::SURF_PERNAMBUCANAS_PRE_RECHARGE,
                'price'     => 0,
                'product'   => $product,
            ]);

        $newsAttributes = $mountNewAttribute->getAttributes($service->toArray());
        $this->assertEquals($valueExpected, data_get($newsAttributes, 'price'));
        $this->assertEquals($product, data_get($newsAttributes, 'label'));
    }

    /** @expectedException \TradeAppOne\Exceptions\BuildExceptions */
    public function test_plan_not_found(): void
    {
        $responseMock = new Response(
            HttpResponse::HTTP_OK,
            [],
            ''
        );

        $adapter = new PagtelPlansResponseAdapter(RestResponse::success($responseMock));

        $this->mockPagtelServiceByResponseAdapter($adapter);

        $mountNewAttribute = resolve(MountNewAttributeFromSurf::class);

        $service = $this->factory->of(SurfPernambucanasPrePago::class)
            ->make([
                'operation' => Operations::SURF_PERNAMBUCANAS_PRE_RECHARGE,
                'price' => 0,
                'product' => 'Plano 30 Coins NotFound',
            ]);

        $mountNewAttribute->getAttributes($service->toArray());
    }

    private function mockPagtelServiceByResponseAdapter(PagtelResponseAdapter $responseAdapter): void
    {
        $this->instance(
            PagtelService::class,
            Mockery::mock(PagtelService::class, function (MockInterface $mock) use ($responseAdapter): void {
                $mock->shouldReceive('plans')
                    ->andReturn($responseAdapter);
            })
        );
    }

    /** @dataProvider provider_activation_plans_to_tests */
    public function test_activation_plan_match(string $product, float $valueExpected): void
    {
        $adapter = $this->mount_adapter_valid_activation_plans_response();

        $this->mockPagtelServiceActivationPlansByResponseAdapter($adapter);

        $mountNewAttribute = resolve(MountNewAttributeFromSurf::class);

        $service = $this->factory->of(SurfPernambucanasPrePago::class)
            ->make([
                'operation' => Operations::SURF_PERNAMBUCANAS_PRE,
                'price'     => 0,
                'product'   => $product,
            ]);

        $newsAttributes = $mountNewAttribute->getAttributes($service->toArray());

        $this->assertEquals($valueExpected, data_get($newsAttributes, 'price'));
        $this->assertEquals($product, data_get($service, 'product'));
    }

    /** @return array[] */
    public function provider_activation_plans_to_tests(): array
    {
        return [
            [
                '8B7880FD-9B17-419D-9EDB-A0EC1E69C1C2',
                25.00
            ],
            [
                'A7322E4B-2517-490B-AEC6-ADE65F3FB7ED',
                30.00
            ],
        ];
    }

    private function mount_adapter_valid_activation_plans_response(): PagtelActivationPlansResponseAdapter
    {
        $responseMock = new Response(
            HttpResponse::HTTP_OK,
            [],
            json_encode([
                'payload'  => [
                    [
                        "plan_id"   => "8B7880FD-9B17-419D-9EDB-A0EC1E69C1C2",
                        "name"      => "Pernambucanas Conectado",
                        "value"     => "2500",
                        "validity"  => "30 dias ",
                        "type"      => "connected-plan",
                    ],
                    [
                        "plan_id"   => "8B7880FD-9B17-419D-9EDB-ABCDEFGHJIK2",
                        "name"      => "Pernambucanas Conectado",
                        "value"     => "3000",
                        "validity"  => "30 dias ",
                        "type"      => "collaborative-plan",
                    ],
                ],
            ])
        );

        return new PagtelActivationPlansResponseAdapter(RestResponse::success($responseMock));
    }

    private function mockPagtelServiceActivationPlansByResponseAdapter(PagtelResponseAdapter $responseAdapter): void
    {
        $this->instance(
            PagtelService::class,
            Mockery::mock(PagtelService::class, function (MockInterface $mock) use ($responseAdapter): void {
                $mock->shouldReceive('activationPlans')
                    ->andReturn($responseAdapter);
            })
        );
    }
}
