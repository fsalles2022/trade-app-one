<?php
declare(strict_types=1);

namespace Bulletin\tests\Unit\Repositories;

use Bulletin\Models\Bulletin;
use Bulletin\Repositories\BulletinRepository;
use Bulletin\Tests\Helpers\Builders\BulletinBuilder;
use Bulletin\tests\Helpers\Functions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Domain\Policies\Authorizations;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class BulletinRepositoryTest extends TestCase
{
    private $bulletinRepository;

    protected function setUp()
    {
        parent::setUp();
        Storage::fake('s3');

        $authorization            = resolve(Authorizations::class);
        $this->bulletinRepository = (new BulletinRepository($authorization));
    }

    public function test_get_all_method(): void
    {
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user        = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        $bulletin    = (new BulletinBuilder())->withNetwork($network)->build();

        $bulletin->user()->sync([$user->id]);

        $mockery = \Mockery::mock(BulletinRepository::class);
        $mockery->shouldReceive('getAll')->andReturn($user->bulletins()->first()->query());

        $builder = $mockery->getAll();

        $this->assertInstanceOf(Builder::class, $builder);
    }

    public function test_save_method(): void
    {
        $network       = (new NetworkBuilder())->build();
        $pointOfSale   = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $user          = (new UserBuilder())->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        $attributes    = Functions::payloadStoreBulletin($network->slug, $pointOfSale->slug, $user->role->slug);
        $makeTestImage = Functions::makeTestImage();

        $imageDesktop =  $makeTestImage['uploadedFile'];

        $bulletins     = $this->bulletinRepository->save([
            'data' => json_encode($attributes),
            'imageDesktop' => $imageDesktop
        ]);
        $bulletin      = $bulletins[0];
        $bulletinArray = $bulletin->toArray();

        $this->assertNotEmpty($bulletins);
        $this->assertInstanceOf(Bulletin::class, $bulletin);
        $this->assertArrayHasKey('title', $bulletinArray);
        $this->assertArrayHasKey('description', $bulletinArray);
        $this->assertArrayHasKey('networkId', $bulletinArray);
        $this->assertArrayHasKey('status', $bulletinArray);
        $this->assertArrayHasKey('urlImage', $bulletinArray);
        $this->assertArrayHasKey('initialDate', $bulletinArray);
        $this->assertArrayHasKey('finalDate', $bulletinArray);

        unlink($makeTestImage['path']);
    }

    public function test_update_method(): void
    {
        $network  = (new NetworkBuilder())->build();
        $bulletin = (new BulletinBuilder())->withNetwork($network)->build();

        $oldTitle = $bulletin->title;
        $newTitle = 'New Title Here...';

        $this->bulletinRepository->update(['title' => $newTitle], $bulletin);

        $this->assertEquals($newTitle, $bulletin->title);
        $this->assertNotEquals($oldTitle, $bulletin->title);
    }
}
