<?php


namespace TradeAppOne\Tests\Unit\Domain\Services;

use ClaroBR\Tests\Helpers\SivFactoriesHelper;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Services\PointOfSaleService;
use TradeAppOne\Exceptions\BusinessExceptions\UserDoesntBelongsToPointOfSaleException;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\ControleBoletoHelper;
use TradeAppOne\Tests\Helpers\ControleFacilHelper;
use TradeAppOne\Tests\Helpers\SaleHelper;
use TradeAppOne\Tests\TestCase;

class PointOfSaleServiceTest extends TestCase
{
    use ControleBoletoHelper, ControleFacilHelper, SaleHelper, SivFactoriesHelper;

    /** @test */
    public function should_return_point_of_sale_instance_when_belongs_to_user() {
        $service       = resolve(PointOfSaleService::class);
        $user          = (new UserBuilder())->build();
        $user->pointsOfSale()->attach((new PointOfSaleBuilder())->build());

        $pointsOfSale  = $service->checkPermissionAndReturnPointOfSale($user, 1);

        $this->assertInstanceOf(PointOfSale::class, $pointsOfSale);
    }

    /** @test */
    public function should_return_point_of_sale_instance_when_is_last_attached() {
        $service       = resolve(PointOfSaleService::class);
        $user          = (new UserBuilder())->build();
        $user->pointsOfSale()->attach((new PointOfSaleBuilder())->build());
        $user->pointsOfSale()->attach((new PointOfSaleBuilder())->build());

        $pointsOfSale  = $service->checkPermissionAndReturnPointOfSale($user, 3);

        $this->assertInstanceOf(PointOfSale::class, $pointsOfSale);
    }

    /** @test */
    public function should_throw_exception_when_point_of_sale_doesnt_belongs_to_user() {
        $service       = resolve(PointOfSaleService::class);
        $user          = (new UserBuilder())->build();

        $this->expectException(UserDoesntBelongsToPointOfSaleException::class);
        $service->checkPermissionAndReturnPointOfSale($user, 10);
    }

    /** @test */
    public function should_return_points_of_sales_of_user_logged_when_call_getUserPointOfSaleLogged()
    {
        $user = (new UserBuilder())->build();
        $service = resolve(PointOfSaleService::class);

        $received = $service->getUserPointOfSaleLogged($user);

        $this->assertCount(1, $received);
    }
}
