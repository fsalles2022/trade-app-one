<?php

namespace ClaroBR\Tests\Unit\Domain\Services;

use ClaroBR\Connection\SivConnection;
use ClaroBR\Models\ControleBoleto;
use ClaroBR\Services\SivService;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Factories\SaleFactory;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Domain\Repositories\Collections\SalePaginatedRepository;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\PointOfSaleService;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Events\PreAnalysisEvent;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\SaleHelper;
use TradeAppOne\Tests\TestCase;

class SaleServiceTest extends TestCase
{
    use SaleHelper;
    use SivBindingHelper;
    use SivFactoriesHelper;

    /** @test */
    public function sale_should_has_array_of_services_when_persistence_works()
    {
        $this->bindMountNewAttributesFromSiv();
        $hierarchy         = (new HierarchyBuilder())->build();
        $pointOfSale       = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper        = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        $requestedServices = $this
            ->sivFactories()
            ->of(ControleBoleto::class)
            ->times(2)
            ->make()
            ->toArray();

        Auth::setUser($userHelper);

        $this->app->bind(PointOfSaleRepository::class, function () use ($pointOfSale) {
            $repository = $this->getMockBuilder(PointOfSaleRepository::class)
                ->disableOriginalConstructor()
                ->setMethods(['find'])
                ->getMock();

            $repository->method('find')->will($this->returnValue($pointOfSale));
            return $repository;
        });

        $this->app->bind(PointOfSaleService::class, function () use ($pointOfSale) {
            $service = $this->getMockBuilder(PointOfSaleService::class)
                ->setConstructorArgs([$this->app->make(PointOfSaleRepository::class)])
                ->setMethods(['authenticatedUserPointsOfSale'])
                ->getMock();

            $service
                ->method('authenticatedUserPointsOfSale')
                ->will($this->returnValue(collect([$pointOfSale])));

            return $service;
        });

        $this->saveSaleWithServicesSucessfull($userHelper, $pointOfSale, $requestedServices);

        $saleService = $this->app->make(SaleService::class);
        $resultSale  = $saleService->new(SubSystemEnum::WEB, $userHelper, $requestedServices, $pointOfSale->id);
        self::assertNotEmpty($resultSale->services);
    }

    public function saveSaleWithServicesSucessfull($userHelper, $pointOfSale, $requestedServices)
    {
        $this->app->bind(SaleRepository::class, function () use ($userHelper, $pointOfSale, $requestedServices) {
            $repository = $this->getMockBuilder(SaleRepository::class)
                ->disableOriginalConstructor()
                ->setMethods(['find', 'update', 'save'])
                ->getMock();

            $saleEntity = SaleFactory::make(SubSystemEnum::WEB, $userHelper, $pointOfSale, $requestedServices);
            $repository->method('find')->will($this->returnValue(null));
            $repository->method('save')->will($this->returnValue($saleEntity));
            return $repository;
        });

        $this->app->bind(PointOfSaleRepository::class, function () use ($pointOfSale) {
            $repository = $this->getMockBuilder(PointOfSaleRepository::class)
                ->disableOriginalConstructor()
                ->setMethods(['find'])
                ->getMock();

            $repository->method('find')->will($this->returnValue($pointOfSale));
            return $repository;
        });
    }

    /** @test */
    public function should_event_be_called_in_claro_credit_analysis()
    {
        Event::fake();

        $responsable = \Mockery::mock(RestResponse::class)->makePartial();
        $responsable->shouldReceive('toArray')->once()->andReturn([]);

        $mock = \Mockery::mock(SivConnection::class)->makePartial();

        $mock->shouldReceive('creditAnalysis')->once()->andReturn($responsable);

        $this->instance(SivConnection::class, $mock);

        $sivService = resolve(SivService::class);

        $customer = ['cpf' => '78416222363', 'firstName' => '3424123424'];
        $sivService->creditAnalysis($customer);

        Event::assertDispatched(PreAnalysisEvent::class);
    }

    public function should_filter_sale_by_date_return_one()
    {
        $sameService = factory(Service::class)->make([
            'operator' => Operations::CLARO,
            'status'   => ServiceStatus::ACCEPTED
        ])->toArray();

        $old  = factory(Sale::class, 2)->create(['createdAt' => now()->subMonth(), 'services' => [$sameService]]);
        $next = factory(Sale::class)->create(['createdAt' => now()->subDay(), 'services' => [$sameService]]);

        $result = resolve(SaleService::class)->getSubmittedSalesToSentinel(
            Operations::CLARO,
            [
            'initialDate' => now()->subDays(2)
            ]
        );

        self::assertCount(1, $result);

        $result = resolve(SaleService::class)->getSubmittedSalesToSentinel(
            Operations::CLARO,
            [
            'initialDate' => now()->subMonths(2)
            ]
        );
        self::assertCount(3, $result);
    }

    /** @test */
    public function should_return_sales_with_context_and_filters(): void
    {
        $userBuilder = (new UserBuilder())
            ->withRoleState('admin')
            ->withPermission(SalePermission::getFullName(SalePermission::CONTEXT_ALL))
            ->build();

        $service = $this->sivFactories()
            ->create(ControleBoleto::class, ['status' => ServiceStatus::APPROVED]);

        (new SaleBuilder())
            ->withServices([$service])
            ->withUser($userBuilder)
            ->build();

        $filtersToService = [
            'status' => ServiceStatus::APPROVED,
            'operator' => Operations::CLARO,
            'operation' => Operations::CLARO_CONTROLE_BOLETO
        ];

        $saleService = $this->app->make(SaleService::class);

        $list = $saleService->filterByContext(
            $userBuilder,
            $filtersToService,
            1,
            SalePaginatedRepository::QUANTITY_PER_PAGE
        );

        $listToArray = $list->toArray();

        $this->assertEquals(1, data_get($listToArray, 'total'));
        $this->assertEquals($filtersToService['status'], data_get($listToArray, 'data.0.services.0.status'));
        $this->assertEquals($filtersToService['operator'], data_get($listToArray, 'data.0.services.0.operator'));
        $this->assertEquals($filtersToService['operation'], data_get($listToArray, 'data.0.services.0.operation'));
    }
}
