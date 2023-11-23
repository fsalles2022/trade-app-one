<?php

namespace TradeAppOne\Tests\Unit\Domain\Importables;

use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Importables\ImportableFactory;
use TradeAppOne\Domain\Importables\ImportableInterface;
use TradeAppOne\Domain\Importables\UserImportable;
use TradeAppOne\Exceptions\BusinessExceptions\ImportableNotFoundException;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ImportableFactoryTest extends TestCase
{
    /** @test */
    public function factory_should_return_via_varejo_importable_interface(): void
    {
        $network = (new NetworkBuilder())->withSlug(NetworkEnum::VIA_VAREJO)->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $role = (new RoleBuilder())->withNetwork($network)->build();
        $userToLogging = (new UserBuilder())->withRole($role)->withPointOfSale($pointOfSale)->build();
        Auth::shouldReceive('user')->once()->andReturn($userToLogging);

        $importable = ImportableFactory::make('USERS');
        self::assertInstanceOf(ImportableInterface::class, $importable);
    }

    /** @test */
    public function factory_should_return_via_varejo_user_importable()
    {
        $network = (new NetworkBuilder())->withSlug(NetworkEnum::VIA_VAREJO)->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $role = (new RoleBuilder())->withNetwork($network)->build();
        $userToLogging = (new UserBuilder())->withRole($role)->withPointOfSale($pointOfSale)->build();
        Auth::shouldReceive('user')->once()->andReturn($userToLogging);

        $importable = ImportableFactory::make('USERS');
        self::assertInstanceOf(UserImportable::class, $importable);
    }

    /** @test */
    public function factory_should_return_GPA_importable_interface(): void
    {
        $network = (new NetworkBuilder())->withSlug(NetworkEnum::GPA)->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $role = (new RoleBuilder())->withNetwork($network)->build();
        $userToLogging = (new UserBuilder())->withRole($role)->withPointOfSale($pointOfSale)->build();
        Auth::shouldReceive('user')->once()->andReturn($userToLogging);

        $importable = ImportableFactory::make('USERS');
        self::assertInstanceOf(ImportableInterface::class, $importable);
    }

    /** @test */
    public function factory_should_return_GPA_user_importable()
    {
        $network = (new NetworkBuilder())->withSlug(NetworkEnum::GPA)->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $role = (new RoleBuilder())->withNetwork($network)->build();
        $userToLogging = (new UserBuilder())->withRole($role)->withPointOfSale($pointOfSale)->build();
        Auth::shouldReceive('user')->once()->andReturn($userToLogging);

        $importable = ImportableFactory::make('USERS');
        self::assertInstanceOf(UserImportable::class, $importable);
    }

    /** @test */
    public function factory_should_return_importable_not_found_exception_when_importable_invalid()
    {
        $this->expectException(ImportableNotFoundException::class);
        $importable = ImportableFactory::make(str_random(5));
    }
}
