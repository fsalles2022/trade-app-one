<?php


namespace TradeAppOne\Tests\Unit\Domain\Services;

use ClaroBR\Models\ControleFacil;
use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use ClaroBR\Tests\ServerTest\SivBindingHelper;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use MongoDB\BSON\ObjectId;
use TradeAppOne\Domain\Enumerators\Channels;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Factories\SaleFactory;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Collections\ServiceTransactionGenerator;
use TradeAppOne\Domain\Models\Tables\AvailableService;
use TradeAppOne\Domain\Models\Tables\Service as ServiceModel;
use TradeAppOne\Domain\Models\Tables\ServiceOption;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\PointOfSaleService;
use TradeAppOne\Domain\Services\Sale\ServiceOptionsFilter;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Events\PreAnalysisEvent;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceAlreadyInProgress;
use TradeAppOne\Exceptions\BusinessExceptions\UserDoesntBelongsToPointOfSaleException;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\ControleBoletoHelper;
use TradeAppOne\Tests\Helpers\ControleFacilHelper;
use TradeAppOne\Tests\Helpers\SaleHelper;
use TradeAppOne\Tests\TestCase;
use VivoBR\Services\MapSunSalesService;

class SaleServiceTest extends TestCase
{
    use ControleBoletoHelper, ControleFacilHelper, SaleHelper, SivFactoriesHelper, SivBindingHelper, AuthHelper;

    /** @test */
    public function return_should_throw_exception_when_user_no_belongs_to_point_of_sale()
    {
        $hierarchy = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        Auth::setUser($userHelper);
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

        $this->expectException(UserDoesntBelongsToPointOfSaleException::class);
        $saleService = $this->app->make(SaleService::class);
        $listOfServices = factory(Service::class)->make()->toArray();

        $saleService->new(SubSystemEnum::WEB, $userHelper, $listOfServices, 'asa');
    }

    /** @test */
    public function return_should_be_instance_of_sale_when_user_belongs_to_point_of_sale()
    {
        $this->bindMountNewAttributesFromSiv();
        $hierarchy = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
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

        $listOfServices = $this->sivFactories()
            ->of(ControleFacil::class)
            ->times(2)
            ->make()
            ->toArray();
        $this->saveSaleWithServicesSucessfull($userHelper, $pointOfSale, $listOfServices);

        $saleService = $this->app->make(SaleService::class);
        $resultSale = $saleService->new(SubSystemEnum::WEB, $userHelper, $listOfServices, $pointOfSale->id);
        self::assertInstanceOf(Sale::class, $resultSale);
    }

    public function saveSaleWithServicesSucessfull($user, $pointOfsale, $requestedServices)
    {
        $this->app->bind(SaleRepository::class, function () use ($user, $pointOfsale, $requestedServices) {
            $repository = $this->getMockBuilder(SaleRepository::class)
                ->disableOriginalConstructor()
                ->setMethods(['find', 'update', 'save'])
                ->getMock();

            $saleEntity = SaleFactory::make(SubSystemEnum::WEB, $user, $pointOfsale, $requestedServices);
            $repository->method('find')->will($this->returnValue(null));
            $repository->method('save')->will($this->returnValue($saleEntity));
            return $repository;
        });
    }

    /** @test */
    public function sale_should_be_null_when_cant_save_in_database()
    {
        $this->bindMountNewAttributesFromSiv();
        $hierarchy = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        $listOfServices = $this->sivFactories()->of(ControleFacil::class)->times(2)->make()->toArray();
        Auth::setUser($userHelper);
        $this->saveSaleWithServicesFailed();

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

        $saleService = $this->app->make(SaleService::class);
        $resultSale = $saleService->new(SubSystemEnum::WEB, $userHelper, $listOfServices, $pointOfSale->id);
        self::assertNull($resultSale);
    }

    public function saveSaleWithServicesFailed()
    {
        $this->app->bind(SaleRepository::class, function () {
            $repository = $this->getMockBuilder(SaleRepository::class)
                ->disableOriginalConstructor()
                ->setMethods(['find', 'update', 'save'])
                ->getMock();

            $repository->method('find')->will($this->returnValue(null));
            $repository->method('save')->will($this->returnValue(null));
            return $repository;
        });
    }

    /** @test */
    public function should_event_be_called_in_save_sale()
    {
        Event::fake();

        $this->bindMountNewAttributesFromSiv();
        $hierarchy = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
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

        $listOfServices = $this->sivFactories()
            ->of(ControleFacil::class)
            ->times(2)
            ->make()
            ->toArray();
        $this->saveSaleWithServicesSucessfull($userHelper, $pointOfSale, $listOfServices);

        $saleService = $this->app->make(SaleService::class);
        $saleService->new(SubSystemEnum::WEB, $userHelper, $listOfServices, $pointOfSale->id);

        Event::assertDispatched(PreAnalysisEvent::class);
    }

    /** @test */
    public function should_throw_exception_when_status_approved()
    {
        $saleTransaction = ServiceTransactionGenerator::generate();
        $serviceTransaction = $saleTransaction . '-0';
        $service = factory(Service::class)->make([
            'status' => ServiceStatus::APPROVED,
            'serviceTransaction' => $serviceTransaction,
        ])->toArray();
        $sale = factory(Sale::class)->create([
            'services' => [$service],
            'saleTransaction' => $saleTransaction,
        ]);
        $this->expectException(ServiceAlreadyInProgress::class);
        $saleService = resolve(SaleService::class);
        $saleService->integrateService(['serviceTransaction' => $serviceTransaction]);
    }

    /** @test */
    public function should_throw_exception_when_status_accepted()
    {
        $saleTransaction = ServiceTransactionGenerator::generate();
        $serviceTransaction = $saleTransaction . '-0';
        $service = factory(Service::class)->make([
            'status' => ServiceStatus::ACCEPTED,
            'serviceTransaction' => $serviceTransaction,
        ])->toArray();
        $sale = factory(Sale::class)->create([
            'services' => [$service],
            'saleTransaction' => $saleTransaction,
        ]);
        $this->expectException(ServiceAlreadyInProgress::class);
        $saleService = resolve(SaleService::class);
        $saleService->integrateService(['serviceTransaction' => $serviceTransaction]);
    }

    /** @test */
    public function should_get_by_network_slug_return_collection()
    {
        $saleService = resolve(SaleService::class);
        $received = $saleService->getByNetworkSlug('34');

        $this->assertInstanceOf(Collection::class, $received);
    }

    /** @test */
    public function should_not_return_triangulation_on_promoter()
    {
        $networkPromoterDistribution = (new NetworkBuilder())
            ->withChannel(Channels::DISTRIBUICAO)
            ->build();
        $pointOfSalePromoterDistribution = (new PointOfSaleBuilder())
            ->withNetwork($networkPromoterDistribution)
            ->build();
        $rolePromoterDistribution = (new RoleBuilder())
            ->withoutNetwork()
            ->build();
        $userPromoterDistribution = (new UserBuilder())
            ->withRole($rolePromoterDistribution)
            ->withoutRoleNetwork()
            ->withPointOfSale($pointOfSalePromoterDistribution)
            ->build();

        $networkPromoterVarejo = (new NetworkBuilder())
            ->withChannel(Channels::VAREJO)
            ->build();
        $pointOfSalePromoterVarejo = (new PointOfSaleBuilder())
            ->withNetwork($networkPromoterVarejo)
            ->build();
        $rolePromoterVarejo = (new RoleBuilder())
            ->withoutNetwork()
            ->build();
        $userPromoterVarejo = (new UserBuilder())
            ->withRole($rolePromoterVarejo)
            ->withoutRoleNetwork()
            ->withPointOfSale($pointOfSalePromoterVarejo)
            ->build();

        $userCommom = (new UserBuilder())->build();

        $this->authAs($userPromoterDistribution)
            ->withHeader('client', SubSystemEnum::WEB)
            ->get('sales/options?sector=LINE_ACTIVATION&operator=CLARO&operation=CONTROLE_FACIL')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMissing([ServiceOptionsFilter::TRIANGULATION_DEVICE]);

        $this->authAs($userPromoterVarejo)
            ->withHeader('client', SubSystemEnum::WEB)
            ->get('sales/options?sector=LINE_ACTIVATION&operator=CLARO&operation=CONTROLE_FACIL')
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([ServiceOptionsFilter::TRIANGULATION_DEVICE]);

        $this->authAs($userCommom)
            ->withHeader('client', SubSystemEnum::WEB)
            ->get('sales/options?sector=LINE_ACTIVATION&operator=CLARO&operation=CONTROLE_FACIL')
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([ServiceOptionsFilter::TRIANGULATION_DEVICE]);
    }

    /** @test */
    public function should_find_service_by_integrator_id()
    {
        $saleTransaction = ServiceTransactionGenerator::generate();
        $serviceTransaction = $saleTransaction . '-0';
        $service = factory(Service::class)->make([
            'serviceTransaction' => $serviceTransaction,
            'operator' => Operations::VIVO,
            'operatorIdentifiers' => [
                'idVenda' => 'SP-395569',
                'idServico' => '405826',
            ],
        ])->toArray();
        $sale = factory(Sale::class)->create([
            'services' => [$service],
            'saleTransaction' => $saleTransaction,
        ]);

        $saleService = resolve(SaleService::class);
        $serviceFound = $saleService->findBySunId('SP-395569', '405826');

        $this->assertEquals($sale->services()->first()->operatorIdentifiers, $serviceFound->operatorIdentifiers);
    }

    /** @test */
    public function should_update_service_with_observations()
    {
        $saleTransaction = ServiceTransactionGenerator::generate();
        $serviceTransaction = $saleTransaction . '-0';
        $service = factory(Service::class)->make([
            '_id' => (new ObjectId())->__toString(),
            'serviceTransaction' => $serviceTransaction,
            'operator' => Operations::VIVO,
            'operatorIdentifiers' => [
                'idVenda' => 'SP-395569',
                'idServico' => '405826',
            ],
        ])->toArray();
        $sale = factory(Sale::class)->create([
            'services' => [$service],
            'saleTransaction' => $saleTransaction,
        ]);

        $payload = [
            'id' => 'SP-395569',
            'status' => 'AGUARDANDO',
            'servicos' => [
                [
                    'id' => '405826',
                ],
            ],
            'observacoes' => [
                [
                    'id' => 2,
                    'motivo' => 'Cliente não atende',
                    'origem' => 'BKO',
                    'dataHora' => '2018-05-14 11:53:54',
                    'observacao' => 'Tentar após 20:00 horas.',
                ],
            ],

        ];

        $service = $sale->services()->first();
        $this->assertArrayNotHasKey('observations', $service->toArray());

        $mapper = resolve(MapSunSalesService::class);
        $saleService = resolve(SaleService::class);

        $attributes = $mapper->mapAttributesToMongo($payload);

        $service = $saleService->updateService($service, $attributes);
        $this->assertArrayHasKey('observations', $service->toArray());
    }

    /** @test */
    public function should_return_carteirizacao_on_allowed_user()
    {
        $serviceOption = factory(ServiceOption::class)->create([
            'action' => ServiceOption::CARTEIRIZACAO
        ]);

        $service = factory(ServiceModel::class)
            ->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'CLARO', 'operation' => 'CLARO_PRE']);

        $network = (new NetworkBuilder())
            ->build();

        $availableService = factory(AvailableService::class)->create([
            'serviceId' => $service,
            'networkId' => $network
        ]);

        $availableService->options()->sync($serviceOption);

        $userCommom = (new UserBuilder())
            ->withNetwork($network)
            ->withPermission(SalePermission::getFullName(SalePermission::ASSOCIATE))
            ->build();

        $this->authAs($userCommom)
            ->withHeader('client', SubSystemEnum::WEB)
            ->get('sales/options?sector=LINE_ACTIVATION&operator=CLARO&operation=CLARO_PRE')
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(["CARTEIRIZACAO"]);
    }

    /** @test */
    public function should_not_return_carteirizacao_when_user_not_allowed()
    {
        $serviceOption = factory(ServiceOption::class)->create([
            'action' => ServiceOption::CARTEIRIZACAO
        ]);

        $service = factory(ServiceModel::class)
            ->create(['sector' => 'LINE_ACTIVATION', 'operator' => 'CLARO', 'operation' => 'CLARO_PRE']);

        $network = (new NetworkBuilder())
            ->build();

        $availableService = factory(AvailableService::class)->create([
            'serviceId' => $service,
            'networkId' => $network
        ]);

        $availableService->options()->sync($serviceOption);

        $userCommom = (new UserBuilder())
            ->withNetwork($network)
            ->build();

        $this->authAs($userCommom)
            ->withHeader('client', SubSystemEnum::WEB)
            ->get('sales/options?sector=LINE_ACTIVATION&operator=CLARO&operation=CLARO_PRE')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMissing(["CARTEIRIZACAO"]);
    }

    public function test_should_return_flag_disabled_autentica(): void
    {
        config(['utils.autentica.isDisabled' => 1]);
        $this->authAs((new UserBuilder())->build())
            ->withHeader('client', SubSystemEnum::WEB)
            ->get('sales/options?sector=LINE_ACTIVATION&operator=CLARO&operation=CONTROLE_FACIL')
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment([ServiceOptionsFilter::DISABLED_AUTENTICA]);
    }

    public function test_not_should_return_flag_disabled_autentica(): void
    {
        config(['utils.autentica.isDisabled' => 0]);
        $this->authAs((new UserBuilder())->build())
            ->withHeader('client', SubSystemEnum::WEB)
            ->get('sales/options?sector=LINE_ACTIVATION&operator=CLARO&operation=CONTROLE_FACIL')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonMissing([ServiceOptionsFilter::DISABLED_AUTENTICA]);
    }
}
