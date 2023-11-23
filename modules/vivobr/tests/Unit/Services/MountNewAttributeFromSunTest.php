<?php

namespace VivoBR\Tests\Unit\Services;

use Illuminate\Database\Eloquent\Factory;
use TradeAppOne\Exceptions\BusinessExceptions\ProductNotFoundException;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;
use VivoBR\Connection\SunConnection;
use VivoBR\Models\VivoInternetMovelPos;
use VivoBR\Services\MountNewAttributeFromSun;

class MountNewAttributeFromSunTest extends TestCase
{
    use ArrayAssertTrait;

    private $factory;
    private $sunConnection;

    protected function setUp()
    {
        parent::setUp();
        $this->factory       = Factory::construct(\Faker\Factory::create(), base_path('modules/vivobr/tests/Factories'));
        $this->sunConnection = \Mockery::mock(SunConnection::class)
            ->shouldReceive('selectCustomConnection')
            ->once()
            ->andReturn(resolve(SunConnection::class))
            ->getMock();
    }

    /** @test */
    public function should_return_product_not_found_exception_when_service_is_vivo_internet_movel_pos_and_product_not_found_in_integration()
    {
        $mountNewAttributeFromSun = new MountNewAttributeFromSun($this->sunConnection);
        $vivoInternetMovelPos     = $this->factory->of(VivoInternetMovelPos::class)->states('invalid_product')->make();

        $this->be((new UserBuilder())->build());
        $this->expectException(ProductNotFoundException::class);
        $mountNewAttributeFromSun->getAttributes($vivoInternetMovelPos->toArray());
    }
}
