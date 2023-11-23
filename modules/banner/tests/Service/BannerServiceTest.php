<?php

namespace Banner\tests\Service;

use Banner\Exceptions\ModelInvalidException;
use Banner\Models\Banner;
use Banner\Repositories\BannerRepository;
use Banner\Service\BannerService;
use Banner\tests\MigrationsTest;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Tests\CreatesApplication;

class BannerServiceTest extends BaseTestCase
{
    use CreatesApplication;

    /** @test */
    public function should_return_banner_instance_of_banner()
    {
        Storage::shouldReceive('disk')->never();
        $pathsReturnedFromS3 = ['http://test.com/image', 'http://test.com/placeholder'];
        $service             = \Mockery::mock(BannerService::class, [resolve(BannerRepository::class)])->makePartial();
        $service->shouldReceive('generatePlaceholder')
            ->andReturn($pathsReturnedFromS3);

        $result = $service->store([
            'label'        => 'test',
            'key'          => 'test',
            'imageDesktop' => UploadedFile::fake()->image('desk'),
            'imageMobile'  => UploadedFile::fake()->image('desk'),
            'imageTablet'  => UploadedFile::fake()->image('desk'),
            'order'        => 1,
            'href'         => 'http://teste.com'
        ]);
        self::assertInstanceOf(Banner::class, $result);
    }

    /** @test */
    public function should_throw_exception_when_key_not_found()
    {
        Storage::shouldReceive('disk')->never();
        $pathsReturnedFromS3 = ['http://test.com/image', 'http://test.com/placeholder'];
        $service             = \Mockery::mock(BannerService::class, [resolve(BannerRepository::class)])->makePartial();
        $service->shouldReceive('generatePlaceholder')
            ->andReturn($pathsReturnedFromS3);

        $this->expectException(ModelInvalidException::class);
        $result = $service->store([
            'label'        => 'test',
            'imageDesktop' => UploadedFile::fake()->image('desk'),
            'imageMobile'  => UploadedFile::fake()->image('desk'),
            'imageTablet'  => UploadedFile::fake()->image('desk'),
            'order'        => 1,
            'href'         => 'http://teste.com'
        ]);
        self::assertInstanceOf(Banner::class, $result);
    }

    /** @test */
    public function should_return_collection_of_1_banner()
    {
        Storage::shouldReceive('disk')->never();
        $pathsReturnedFromS3 = ['http://test.com/image', 'http://test.com/placeholder'];
        $service             = \Mockery::mock(BannerService::class, [resolve(BannerRepository::class)])->makePartial();
        $service->shouldReceive('generatePlaceholder')
            ->andReturn($pathsReturnedFromS3);

        $result = $service->store([
            'label'        => 'test',
            'key'          => 'test',
            'imageDesktop' => UploadedFile::fake()->image('desk'),
            'imageMobile'  => UploadedFile::fake()->image('desk'),
            'imageTablet'  => UploadedFile::fake()->image('desk'),
            'order'        => 1,
            'href'         => 'http://teste.com'
        ]);
        self::assertCount(1, $service->getAll('test'));
        self::assertInstanceOf(Collection::class, $service->getAll('test'));
    }

    /** @test */
    public function should_save_banner_end_at_default()
    {
        Storage::shouldReceive('disk')->never();
        $pathsReturnedFromS3 = ['http://test.com/image', 'http://test.com/placeholder'];
        $service             = \Mockery::mock(BannerService::class, [resolve(BannerRepository::class)])->makePartial();
        $service->shouldReceive('generatePlaceholder')
            ->andReturn($pathsReturnedFromS3);

        $result = $service->store([
            'label'        => 'test',
            'key'          => 'test',
            'imageDesktop' => UploadedFile::fake()->image('desk'),
            'imageMobile'  => UploadedFile::fake()->image('desk'),
            'imageTablet'  => UploadedFile::fake()->image('desk'),
            'order'        => 1,
            'href'         => 'http://teste.com'
        ]);
        self::assertEquals($result->end_at, Carbon::now()->toDateTimeString());
        self::assertEquals($result->start_at, Carbon::now()->toDateTimeString());
    }

    /** @test */
    public function should_save_banner_end_at_default_and_no_return_list_because_end_at()
    {
        Storage::shouldReceive('disk')->never();
        $pathsReturnedFromS3 = ['http://test.com/image', 'http://test.com/placeholder'];
        $service             = \Mockery::mock(BannerService::class, [resolve(BannerRepository::class)])->makePartial();
        $service->shouldReceive('generatePlaceholder')
            ->andReturn($pathsReturnedFromS3);

        $result = $service->store([
            'label'        => 'test',
            'key'          => 'test',
            'imageDesktop' => UploadedFile::fake()->image('desk'),
            'imageMobile'  => UploadedFile::fake()->image('desk'),
            'imageTablet'  => UploadedFile::fake()->image('desk'),
            'endAt'        => Carbon::now()->subDay(),
            'startAt'      => Carbon::now()->subDays(3),
            'order'        => 1,
            'href'         => 'http://teste.com'
        ]);

        self::assertCount(0, $service->getAll('test'));
    }

    protected function setUp()
    {
        parent::setUp();
        (new MigrationsTest())->up();
    }

    protected function tearDown()
    {
        (new MigrationsTest())->down();
        parent::tearDown();
    }
}
