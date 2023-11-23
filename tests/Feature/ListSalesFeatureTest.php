<?php


namespace TradeAppOne\Tests\Feature;

use Buyback\Tests\Helpers\TradeInServices;
use ClaroBR\Tests\Helpers\ClaroServices;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Factories\MongoDbConnector;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Domain\Repositories\Collections\SalePaginatedRepository;
use TradeAppOne\Http\Resources\PointOfSaleResource;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\SaleHelper;
use TradeAppOne\Tests\TestCase;
use Illuminate\Http\Response as Resp;


class ListSalesFeatureTest extends TestCase
{
    use AuthHelper, SaleHelper;

    protected $endpoint = '/sales/list';

    public function post_should_return_list_of_3_sales_with_services_filled_when_request()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        factory(Sale::class, 3)->create([
            'user'        => $userHelper->toArray(),
            'pointOfSale' => (new PointOfSaleResource())->map($userHelper->pointsOfSale->first())
        ]);

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('GET', $this->endpoint);
        $response->assertJsonStructure(['data' => [['services'], ['services']]]);
    }

    /** @test */
    public function post_should_return_paginated_list_when_search_by_salesman_cpf()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        $this->factorySales(3, $userHelper, $pointOfSale);

        $anotherUser = (new UserBuilder())->build();
        $this->factorySales(10, $anotherUser, $pointOfSale);

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', "$this->endpoint?cpfSalesman=$userHelper->cpf");

        $response->assertJsonFragment(['total' => 3]);
    }

    /** @test */
    public function post_should_return_list_of_3_sales_when_user_has_CONTEXT_ALL()
    {
        $network           = (new NetworkBuilder())->build();
        $permission        = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::CONTEXT_ALL)
            ]);
        $userHasContextAll = (new UserBuilder())->withPermissions([$permission])->withNetwork($network)->build();
        $hierarchy         = (new HierarchyBuilder())->withUser($userHasContextAll)->build();

        $networkTwo     = (new NetworkBuilder())->build();
        $pointOfSaleTwo = (new PointOfSaleBuilder())->withNetwork($networkTwo)->withHierarchy($hierarchy)->build();

        $SalesmanTwo = (new UserBuilder())->withNetwork($networkTwo)->withPointOfSale($pointOfSaleTwo)->build();
        $this->factorySales(1, $SalesmanTwo, $pointOfSaleTwo);

        $networkThree     = (new NetworkBuilder())->build();
        $pointOfSaleThree = (new PointOfSaleBuilder())->withNetwork($networkThree)->withHierarchy($hierarchy)->build();
        $SalesmanThree    = (new UserBuilder())->withNetwork($networkThree)->withPointOfSale($pointOfSaleThree)->build();
        $this->factorySales(1, $SalesmanThree, $pointOfSaleThree);

        $networkFour     = (new NetworkBuilder())->build();
        $pointOfSaleFour = (new PointOfSaleBuilder())->withNetwork($networkFour)->withHierarchy($hierarchy)->build();
        $SalesmanFour    = (new UserBuilder())->withNetwork($networkFour)->withPointOfSale($pointOfSaleFour)->build();
        $this->factorySales(1, $SalesmanFour, $pointOfSaleFour);

        $token = $this->loginUser($userHasContextAll);

        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint);
        $response->assertJsonFragment(['total' => 3]);
    }

    /** @test */
    public function post_should_return_list_of_3_sales_when_user_has_CONTEXT_HIERARCHY()
    {
        $network                      = (new NetworkBuilder())->build();
        $permission                   = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::CONTEXT_HIERARCHY)
            ]);
        $otherUserHasContextHierarchy = (new UserBuilder())->withPermissions([$permission])->withNetwork($network)->build();
        $hierarchyTwo                 = (new HierarchyBuilder())->withUser($otherUserHasContextHierarchy)->build();

        $pointOfSaleFive = (new PointOfSaleBuilder())->withNetwork($network)->withHierarchy($hierarchyTwo)->build();

        $userSalesmanFour = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSaleFive)->build();
        $this->factorySales(1, $userSalesmanFour, $pointOfSaleFive);

        $userHasContextHierarchy = (new UserBuilder())->withPermissions([$permission])->withNetwork($network)->build();
        $hierarchy               = (new HierarchyBuilder())->withParent($hierarchyTwo)->withUser($userHasContextHierarchy)->build();

        $pointOfSaleTwo = (new PointOfSaleBuilder())->withNetwork($network)->withHierarchy($hierarchy)->build();
        $userSalesman   = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSaleTwo)->build();
        $this->factorySales(1, $userSalesman, $pointOfSaleTwo);

        $pointOfSaleThree = (new PointOfSaleBuilder())->withNetwork($network)->withHierarchy($hierarchy)->build();
        $userSalesmanTwo  = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSaleThree)->build();
        $this->factorySales(1, $userSalesmanTwo, $pointOfSaleThree);

        $pointOfSaleFour   = (new PointOfSaleBuilder())->withNetwork($network)->withHierarchy($hierarchy)->build();
        $userSalesmanThree = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSaleFour)->build();
        $this->factorySales(1, $userSalesmanThree, $pointOfSaleFour);

        $token    = $this->loginUser($userHasContextHierarchy);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint);
        $response->assertJsonFragment(['total' => 3]);
    }

    /** @test */
    public function post_should_return_list_of_3_sales_when_user_has_CONTEXT_NON_EXISTENT()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $permission  = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::CONTEXT_NON_EXISTENT)
            ]);
        $userHelper  = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();

        $this->factorySales(3, $userHelper, $pointOfSale);

        $pointOfSaleTwo = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $anotherUser    = (new UserBuilder())->withPointOfSale($pointOfSaleTwo)->build();
        $this->factorySales(3, $anotherUser, $pointOfSaleTwo);

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint);
        $response->assertJsonFragment(['total' => 3]);
    }

    /** @test */
    public function post_should_return_list_of_3_sales_when_user_has_CONTEXT_NON_EXISTENT_and_filter_imei()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $permission  = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::CONTEXT_NON_EXISTENT)
            ]);
        $userHelper  = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();
        $assertImei  = '111111111111111';
        $this->factorySales(3, $userHelper, $pointOfSale);

        $pointOfSaleTwo = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $anotherUser    = (new UserBuilder())->withPointOfSale($pointOfSaleTwo)->build();
        $service        = factory(Service::class)->make(['imei' => $assertImei])->toArray();
        factory(Sale::class)->create([
            'pointOfSale' => $pointOfSale->toArray(),
            'user'        => $userHelper->toArray(),
            'services'    => [$service]
        ]);

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint, ['imei' => '111111111111111']);

        $response->assertJsonFragment(['total' => 1]);
    }

    /** @test */
    public function post_should_return_list_of_2_sales_when_user_has_CONTEXT_ALL_and_filter_operator()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();

        $permission = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::CONTEXT_ALL)
            ]);
        $userHelper = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();

        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([ClaroServices::ControleBoleto()])
            ->build();

        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([TradeInServices::SaldaoInformaticaMobile()])
            ->build();

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint, ['operator' => Operations::CLARO]);

        $response->assertJsonFragment(['total' => 1]);
    }

    /** @test */
    public function post_should_return_list_of_2_sales_when_user_has_CONTEXT_ALL_and_filter_sale_id()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();

        $permission = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::CONTEXT_ALL)
            ]);
        $userHelper = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();

        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([ClaroServices::ControleBoleto()])
            ->build();

        $sale = (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([TradeInServices::SaldaoInformaticaMobile()])
            ->build();

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint, ['saleId' => $sale->saleTransaction]);

        $response->assertJsonFragment(['total' => 1]);
    }

    /** @test */
    public function post_should_return_list_of_2_sales_when_user_has_CONTEXT_ALL_and_filter_sale_id_partial()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();

        $permission = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::CONTEXT_ALL)
            ]);
        $userHelper = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();

        $sale = (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([TradeInServices::SaldaoInformaticaMobile()])
            ->build();

        $partialSaleTransaction = substr($sale->saleTransaction, 0, 10);
        $token                  = $this->loginUser($userHelper);
        $response               = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint, ['saleId' => $partialSaleTransaction]);

        $response->assertJsonFragment(['total' => 1]);
    }

    /** @test */
    public function post_should_return_list_of_2_sales_when_user_has_CONTEXT_ALL_and_filter_cpf_customer()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $permission  = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::CONTEXT_ALL)
            ]);
        $userHelper  = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();

        $controleBoleto = ClaroServices::ControleBoleto();

        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([$controleBoleto])
            ->build();

        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([TradeInServices::SaldaoInformaticaMobile()])
            ->build();

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint, ['cpfCustomer' => $controleBoleto->customer['cpf']]);

        $response->assertJsonFragment(['total' => 1]);
    }

    /** @test */
    public function post_should_return_list_of_2_sales_when_user_has_CONTEXT_ALL_and_filter_by_first_name()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $permission  = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::CONTEXT_ALL)
            ]);
        $userHelper  = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();

        $controleBoleto = ClaroServices::ControleBoleto();

        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([$controleBoleto])
            ->build();

        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([TradeInServices::SaldaoInformaticaMobile()])
            ->build();

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint, ['name' => $controleBoleto->customer['firstName']]);

        $response->assertJsonFragment(['total' => 1]);
    }

    /** @test */
    public function post_should_return_list_of_2_sales_when_user_has_CONTEXT_ALL_and_filter_by_last_name()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $permission  = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::CONTEXT_ALL)
            ]);
        $userHelper  = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();

        $controleBoleto = ClaroServices::ControleBoleto();

        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([$controleBoleto])
            ->build();

        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([TradeInServices::SaldaoInformaticaMobile()])
            ->build();

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint, ['name' => $controleBoleto->customer['lastName']]);

        $response->assertJsonFragment(['total' => 1]);
    }

    /** @test */
    public function post_should_return_list_of_2_sales_when_user_has_CONTEXT_ALL_and_go_to_page_2()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();

        $permission = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::CONTEXT_ALL)
            ]);
        $userHelper = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();

        Collection::times(10, function () use ($pointOfSale) {
            (new SaleBuilder())
                ->withPointOfSale($pointOfSale)
                ->withServices([ClaroServices::ControleBoleto()])
                ->build();
        });

        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([TradeInServices::SaldaoInformaticaMobile()])
            ->build();

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint . '/?page=2');

        $services = (data_get($response->json(), 'data'));

        $this->assertEquals(1, count($services));
    }

    /** @test */
    public function post_should_return_page_1_when_user_has_CONTEXT_ALL_and_send_negative_page()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();

        $permission = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::CONTEXT_ALL)
            ]);
        $userHelper = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();

        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([TradeInServices::SaldaoInformaticaMobile()])
            ->build();

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint . '/?page=-1');

        $services = (data_get($response->json(), 'data'));

        $this->assertEquals(1, count($services));
    }

    /** @test */
    public function post_should_return_trade_in_based_on_network_service()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $service = factory(\TradeAppOne\Domain\Models\Tables\Service::class)->create([
            'sector' => Operations::TRADE_IN,
            'operator' => Operations::TRADE_IN_MOBILE,
            'operation' => Operations::SALDAO_INFORMATICA]);

        $pointOfSale = (new PointOfSaleBuilder())
            ->withServices($service)
            ->withHierarchy($hierarchy)
            ->build();

        $permission = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::VIEW_ONLY_TRADE_IN)
            ]);
        $userHelper = (new UserBuilder())
            ->withPermissions([$permission])
            ->withPointOfSale($pointOfSale)
            ->build();

        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([TradeInServices::SaldaoInformaticaMobile()])
            ->build();

        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([ClaroServices::ClaroBandaBarga()])
            ->build();

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint);

        $services = (data_get($response->json(), 'data'));

        $this->assertEquals(1, count($services));
    }

    /** @test */
    public function post_should_return_list_of_2_sales_when_user_is_appraiser_and_go_to_page_2()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $service = factory(\TradeAppOne\Domain\Models\Tables\Service::class)->create([
            'sector' => Operations::TRADE_IN,
            'operator' => Operations::TRADE_IN_MOBILE,
            'operation' => Operations::SALDAO_INFORMATICA]);

        $pointOfSale = (new PointOfSaleBuilder())->withServices($service)->withHierarchy($hierarchy)->build();

        $permission = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::VIEW_ONLY_TRADE_IN)
            ]);
        $userHelper = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();

        Collection::times(10, function () {
            (new SaleBuilder())
                ->withServices([TradeInServices::SaldaoInformaticaMobile()])
                ->build();
        });

        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([TradeInServices::SaldaoInformaticaMobile()])
            ->build();

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint . '/?page=2');

        $services = (data_get($response->json(), 'data'));

        $this->assertEquals(1, count($services));
    }

    /** @test */
    public function post_sales_should_return_buyback()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $service = factory(\TradeAppOne\Domain\Models\Tables\Service::class)->create([
            'sector' => Operations::TRADE_IN,
            'operator' => Operations::TRADE_IN_MOBILE,
            'operation' => Operations::SALDAO_INFORMATICA]);
        $pointOfSale = (new PointOfSaleBuilder())->withServices($service)->withHierarchy($hierarchy)->build();
        $permission  = factory(Permission::class)->create([
            'client' => SubSystemEnum::API,
            'slug'   => SalePermission::getFullName(PermissionActions::VIEW_ONLY_TRADE_IN)
        ]);
        $userHelper  = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();
        (new SaleBuilder())->withServices([TradeInServices::SaldaoInformaticaMobile()])->build();

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint);

        $service = (data_get($response->json(), 'data.0.services.0'));

        $this->assertEquals(Operations::TRADE_IN, $service['sector']);
        $this->assertEquals(Operations::SALDAO_INFORMATICA, $service['operation']);
    }

    /** @test */
    public function post_should_return_only_buyback_sales()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $service = factory(\TradeAppOne\Domain\Models\Tables\Service::class)->create([
            'sector' => Operations::TRADE_IN,
            'operator' => Operations::TRADE_IN_MOBILE,
            'operation' => Operations::SALDAO_INFORMATICA]);
        $pointOfSale = (new PointOfSaleBuilder())->withServices($service)->withHierarchy($hierarchy)->build();
        $permission  = factory(Permission::class)->create([
            'client' => SubSystemEnum::API,
            'slug'   => SalePermission::getFullName(PermissionActions::VIEW_ONLY_TRADE_IN)
        ]);
        $userHelper  = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();

        (new SaleBuilder())->withServices([TradeInServices::SaldaoInformaticaMobile()])->build();
        (new SaleBuilder())->withServices([ClaroServices::ControleBoleto()]);

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint);

        $services = (data_get($response->json(), 'data'));

        $this->assertEquals(1, count($services));
    }

    /** @test */
    public function post_should_return_only_one_imei_same_sale(): void
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $permission  = factory(Permission::class)->create([
            'client' => SubSystemEnum::API,
            'slug'   => SalePermission::getFullName(SalePermission::CONTEXT_ALL)
        ]);
        $userHelper  = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();

        $service1       = ClaroServices::ControleBoleto();
        $service1->imei = '000000000000110';
        $service2       = ClaroServices::ControleBoleto();
        $service2->imei = '000000000000220';
        $sale           = (new SaleBuilder())->withServices([$service1, $service2])->build();

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('GET', 'sales', ['imei' => '000000000000110']);

        $services = (data_get($response->json(), 'data'));

        $this->assertEquals(1, count($services));
        $this->assertEquals(1, count(data_get($services[0], 'services')));
        $this->assertEquals('000000000000110', data_get($services[0], 'services.0.imei'));

        $response = $this
            ->withHeader('Authorization', $token)
            ->json('GET', 'sales', ['imei' => '000000000000220']);

        $services = (data_get($response->json(), 'data'));
        $this->assertEquals(1, count($services));
        $this->assertEquals(1, count(data_get($services[0], 'services')));
        $this->assertEquals('000000000000220', data_get($services[0], 'services.0.imei'));
    }

    /** @test */
    public function post_should_return_numero_acesso_when_ntc_is_valid()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $permission  = factory(Permission::class)
            ->create([
                'client' => SubSystemEnum::API,
                'slug'   => SalePermission::getFullName(SalePermission::CONTEXT_NON_EXISTENT)
            ]);
        $userHelper  = (new UserBuilder())->withPermissions([$permission])->withPointOfSale($pointOfSale)->build();

        $ret = $this->factorySales(1,$userHelper, $pointOfSale);

        factory(Sale::class)->create([
            'pointOfSale' => $pointOfSale->toArray(),
            'user'        => $userHelper->toArray(),
            'services'    => factory(Service::class)->make(['log' => ['numeroAcesso' => '13988169260']])->toArray()
        ]);

        $token    = $this->loginUser($userHelper);
        $response = $this
            ->withHeader('Authorization', $token)
            ->json('POST', $this->endpoint, ['ntc' => '13988169260'])
            ->assertStatus(Resp::HTTP_OK)
            ->assertJsonFragment(['total' => 1])
            ->assertJsonFragment(['numeroAcesso' => '13988169260']);

    }

    public function tearDown()
    {
        (resolve(MongoDbConnector::class)
            ->getCollection(SalePaginatedRepository::COLLECTION_NAME)
            ->drop());

        parent::tearDown();
    }
}
