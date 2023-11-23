<?php

namespace ClaroBR\Tests\Unit\OperationAssistants;

use ClaroBR\Connection\SivConnectionInterface;
use ClaroBR\Models\ClaroPos;
use ClaroBR\OperationAssistances\ClaroPosAssistance;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Factories\SaleFactory;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RestResponseBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ClaroPosAssistantTest extends TestCase
{
    use SivFactoriesHelper, SivBindingHelper;

    /** @test */
    public function should_return_true_when_service_response_return_with_protocol()
    {
        $this->bindMountNewAttributesFromSiv();
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        Auth::setUser($userHelper);

        $service    = $this->sivFactories()
            ->of(ClaroPos::class)
            ->make()
            ->toArray();
        $saleEntity = SaleFactory::make(SubSystemEnum::WEB, $userHelper, $pointOfSale, [$service]);
        $this->bindIncompleteRepositories($saleEntity);
        $sivSaleAssistance = app()->make(ClaroPosAssistance::class);
        $rest              = $this->getMockBuilder(RestResponse::class)->setMethods(['toArray'])->getMock();

        $rest->method('toArray')
            ->willReturn(['type' => 'success', 'data' => ['protocol' => '2018506496871', 'status' => 'SUCESSO']]);

        self::assertTrue($sivSaleAssistance->checkSaleIsActivatedByPayload($rest));
    }

    public function bindIncompleteRepositories(Sale $saleEntity)
    {
        $this->app->bind(SaleRepository::class, function () use ($saleEntity) {
            $repository = $this->getMockBuilder(SaleRepository::class)
                ->disableOriginalConstructor()
                ->setMethods(['findInSale', 'find', 'updateService'])
                ->getMock();

            $service                      = $saleEntity->services()->first();
            $service->operatorIdentifiers = [];

            $repository->method('find')->will($this->returnValue($saleEntity));
            $repository->method('findInSale')->will($this->returnValue($service));
            $repository->method('updateService')->will($this->returnValue($service));
            return $repository;
        });
    }

    /** @test */
    public function should_return_false_when_service_response_dont_return_with_protocol()
    {
        $this->bindMountNewAttributesFromSiv();
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        Auth::setUser($userHelper);
        $service    = $this->sivFactories()
            ->of(ClaroPos::class)
            ->make()
            ->toArray();
        $saleEntity = SaleFactory::make(SubSystemEnum::WEB, $userHelper, $pointOfSale, [$service]);
        $this->bindIncompleteRepositories($saleEntity);
        $sivSaleAssistance = app()->make(ClaroPosAssistance::class);
        $rest              = $this->getMockBuilder(RestResponse::class)->setMethods(['toArray'])->getMock();

        $rest->method('toArray')
            ->willReturn(['type' => 'success', 'data' => ['msisdns' => []]]);
        self::assertFalse($sivSaleAssistance->checkSaleIsActivatedByPayload($rest));
    }

    /** @test */
    public function should_return_service_with_provisional_number_when_portability()
    {
        $this->bindMountNewAttributesFromSiv();
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        Auth::setUser($userHelper);

        $service = $this->sivFactories()
            ->of(ClaroPos::class)
            ->states('portability')
            ->make()
            ->toArray();

        $saleEntity = SaleFactory::make(SubSystemEnum::WEB, $userHelper, $pointOfSale, [$service]);
        $this->bindIncompleteRepositories($saleEntity);
        $sivSaleAssistance = app()->make(ClaroPosAssistance::class);
        $rest              = $this->getMockBuilder(RestResponse::class)->setMethods(['toArray'])->getMock();

        $rest->method('toArray')
            ->willReturn(['type' => 'success', 'data' => ['msisdns' => []]]);
        $this->bindIncompleteRepositories($saleEntity);
        $updated = $sivSaleAssistance->saveMsisdn($saleEntity->services()->first(), '1198983872394');

        self::assertArrayHasKey('portedNumber', $updated->toArray());
    }

    /** @test */
    public function should_save_in_service_msisdn_when_enviado_pedido_gerar()
    {
        $this->mockPedidoGerar(1);
        $sale = $this->createSale();

        $assistant = resolve(ClaroPosAssistance::class);
        $assistant->activate($sale->services->first(), []);

        $this->assertDatabaseHas('sales', [
            'saleTransaction' => $sale->saleTransaction,
            'services.0.msisdn' => '67999349144'
            ], 'mongodb');
    }

    private function mockPedidoGerar(int $gerar)
    {
        $sivService = [
            'data' => [
                'data' => [
                    0 => [
                        'services' => [
                            0 => [
                                'enviado_pedido_gerar' => $gerar,
                                'numero_acesso' => '+5567999349144'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $rest = (new RestResponseBuilder())
            ->withBodyFromArray($sivService)
            ->success();

        app()->singleton(SivConnectionInterface::class, function ($app) use ($rest) {
            $conn = \Mockery::mock(SivConnectionInterface::class)->makePartial();
            $conn->shouldReceive('queryUserSales')->andReturn($rest);
            $conn->shouldReceive('activate')->andReturn($rest);

            return $conn;
        });
    }

    private function createSale()
    {
        $service = $this->sivFactories()
            ->of(ClaroPos::class)
            ->make([
                'operatorIdentifiers' => [
                    'servico_id' => '12345',
                    'venda_id'   => '54321'
                ]
            ]);

        $sale       = (new SaleBuilder())->withServices([$service])->build();
        $repository = resolve(SaleRepository::class);
        return $repository->find($sale->saleTransaction);
    }
}
