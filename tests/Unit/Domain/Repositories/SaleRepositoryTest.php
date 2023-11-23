<?php

namespace TradeAppOne\Tests\Unit\Domain\Repositories;

use ClaroBR\Services\MountNewAttributeFromSiv;
use ClaroBR\Tests\Helpers\ClaroServices;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Factories\ServicesFactory;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\HierarchyRepository;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\MountNewAttributesService;
use TradeAppOne\Http\Resources\UserResource;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\ControleBoletoHelper;
use TradeAppOne\Tests\Helpers\SaleHelper;
use TradeAppOne\Tests\TestCase;

class SaleRepositoryTest extends TestCase
{
    use ControleBoletoHelper, SaleHelper;

    /** @test */
    public function sale_should_save_with_timestamps_when_saved_with_services()
    {
        $mock = $this->getMockBuilder(MountNewAttributesService::class)
            ->setMethods(['getAttributes'])->getMock();
        $mock->method('getAttributes')->will($this->returnValue([]));
        $this->app->bind(MountNewAttributeFromSiv::class, function () use ($mock) {
            return $mock;
        });
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        $saleRepository = new SaleRepository(app()->make(HierarchyRepository::class));
        $service        = ServicesFactory::make($this->getControleBoletoFilled());
        $saleEntity     = new Sale([
            'user'        => UserResource::make($userHelper)->resolve(),
            'pointOfSale' => $pointOfSale,
            'services'    => [$service],
        ]);

        $sale = $saleRepository->save($saleEntity);

        $salePersisted = $saleRepository->find($sale->transaction);

        self::assertNotEmpty($salePersisted->createdAt);
        self::assertNotEmpty($salePersisted->updatedAt);
    }

    /** @test */
    public function should_update_service_return_service_entity()
    {
        $saleRepository = new SaleRepository(resolve(HierarchyRepository::class));

        $received = $saleRepository->updateService(new Service(), []);

        $this->assertInstanceOf(Service::class, $received);
    }

    /** @test */
    public function should_update_service_return_service_updated()
    {
        $saleRepository = new SaleRepository(resolve(HierarchyRepository::class));

        $instance = new Service([
            'serviceTransaction' => '12345',
            'value' => 'old_value'
        ]);

        $sale = new Sale();
        $sale->services()->associate($instance);
        $sale->save();

        $expected = 'new_value';
        $received = $saleRepository->updateService($instance, [
            'value' => $expected
        ]);

        $this->assertEquals($received->value, $expected);
    }

    /** @test */
    public function should_update_sale_return_sale_entity()
    {
        $saleRepository = resolve(SaleRepository::class);

        $received = $saleRepository->updateSale(new Sale(), []);

        $this->assertInstanceOf(Sale::class, $received);
    }

    /** @test */
    public function should_update_sale_return_sale_updated()
    {
        $saleRepository = resolve(SaleRepository::class);
        $saleEntity     = new Sale(['user' => 'value']);
        $saleEntity->save();

        $expected = 'new value';
        $received = $saleRepository->updateSale($saleEntity, ['user' => '' . $expected]);

        $this->assertEquals($received->user, $expected);
    }


    /** @test */
    public function should_return_length_aware()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        (new SaleBuilder())
            ->withUser($userHelper)
            ->withPointOfSale($pointOfSale)
            ->withServices([ClaroServices::ControleBoleto()]);

        Auth::setUser($userHelper);
        $saleRepository = new SaleRepository(resolve(HierarchyRepository::class));
        $received       = $saleRepository->paginate([], 10);

        $this->assertEquals(LengthAwarePaginator::class, get_class($received));
    }

    /** @test */
    public function should_return_one_item()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        (new SaleBuilder())
            ->withUser($userHelper)
            ->withPointOfSale($pointOfSale)
            ->withServices([ClaroServices::ControleBoleto()])
            ->build();

        Auth::setUser($userHelper);
        $saleRepository = new SaleRepository(resolve(HierarchyRepository::class));

        $received = $saleRepository->paginate([], 10);

        $this->assertEquals(1, count($received->items()));
    }

    /** @test */
    public function should_return_own_user_sales()
    {
        $hierarchy   = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        $userHelper  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        (new SaleBuilder())
            ->withServices([ClaroServices::ControleBoleto()])
            ->build();

        (new SaleBuilder())
            ->withUser($userHelper)
            ->withPointOfSale($pointOfSale)
            ->withServices([ClaroServices::ControleBoleto()])
            ->build();

        Auth::setUser($userHelper);
        $saleRepository = new SaleRepository(resolve(HierarchyRepository::class));

        $received = $saleRepository->paginate([], 10);

        $this->assertEquals(1, count($received->items()));
    }
}
