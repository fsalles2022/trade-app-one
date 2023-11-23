<?php

declare(strict_types=1);

namespace Bulletin\Tests\Unit\Services;

use Bulletin\Models\Bulletin;
use Bulletin\Service\BulletinServices;
use Bulletin\Tests\Helpers\Builders\BulletinBuilder;
use Bulletin\tests\Helpers\Functions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class BulletinServicesTest extends TestCase
{
    private $bulletinServicesMock;
    private $bulletin;

    protected function setUp()
    {
        parent::setUp();

        $network        = (new NetworkBuilder())->build();
        $this->bulletin = (new BulletinBuilder())->withNetwork($network)->build();

        $this->bulletinServicesMock = Mockery::mock(BulletinServices::class);
    }

    public function test_method_get_bulletins_should_return_builder(): void
    {
        $builder = resolve(Builder::class);
        $this->bulletinServicesMock->shouldReceive('getBulletins')->andReturn($builder);
        $this->assertEquals($builder, $this->bulletinServicesMock->getBulletins());
    }

    public function method_register_bulletin_should_return_bulletin_model(): void
    {
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        $bulletin    = (new BulletinBuilder())->withNetwork($network)->build();
        $attributes  = Functions::payloadStoreBulletin($network->slug, $pointOfSale->slug, $user->role->slug);

        $this->bulletinServicesMock->shouldReceive('registerBulletins')->andReturn([$bulletin]);

        $this->assertInstanceOf(
            Bulletin::class,
            $this->bulletinServicesMock->registerBulletins($attributes)[0]
        );
    }

    public function test_method_update_should_return_bulletin_model(): void
    {
        $this->bulletinServicesMock->shouldReceive('update')->andReturn(true);
        $this->assertTrue($this->bulletinServicesMock->update(['title' => 'New Title Here...'], $this->bulletin));
    }

    /**
     * @throws \Throwable
     */
    public function test_method_change_activation_status(): void
    {
        $this->bulletinServicesMock->shouldReceive('changeActivationStatus')->andReturn(true);
        $this->assertTrue($this->bulletinServicesMock->changeActivationStatus(['status' => true,], $this->bulletin));
    }

    /** @throws \Exception */
    public function test_method_delete_should_return(): void
    {
        $this->bulletinServicesMock->shouldReceive('delete')->andReturn(true);
        $this->assertTrue($this->bulletinServicesMock->delete($this->bulletin));
    }

    /** @throws \Exception */
    public function test_method_bulletinByUser_should_return(): void
    {
        $collection = Collection::make([
            [
                "id" => 6,
                "bulletinsUsers" => [
                    "userId" => 1,
                    "bulletinId" => 1,
                    "seen" => false
                ]
            ]
        ]);

        $this->bulletinServicesMock->shouldReceive('bulletinByUser')->andReturn($collection);

        $this->assertInstanceOf(Collection::class, $this->bulletinServicesMock->bulletinByUser());
    }
}
