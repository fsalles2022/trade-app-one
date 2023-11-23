<?php

namespace Banner\tests\Repositories;

use Banner\Models\Banner;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use TradeAppOne\Tests\CreatesApplication;

class BannerRepositoryTest extends BaseTestCase
{
    use CreatesApplication;

    /** @test */
    public function should_return_null()
    {
        $objectToUpdate = new \StdClass();
        DB::shouldReceive('table')->withAnyArgs()->andReturnSelf();
        DB::shouldReceive('insertGetId')->withAnyArgs()->andReturn(0);
        DB::shouldReceive('find')->never();

        $repository = new \Banner\Repositories\BannerRepository();
        $result     = $repository->save($objectToUpdate);
        self::assertEquals(null, $result);
    }

    /** @test */
    public function should_return_banner_instance()
    {
        $objectToUpdate = new \StdClass();
        DB::shouldReceive('table')->withAnyArgs()->andReturnSelf();
        DB::shouldReceive('insertGetId')->withAnyArgs()->andReturnSelf(1);
        $mck = DB::shouldReceive('find')->withAnyArgs()->andReturnSelf();
        DB::shouldReceive('table')->withAnyArgs()->andReturn($mck);

        $repository = new \Banner\Repositories\BannerRepository();
        $result     = $repository->save($objectToUpdate);
        self::assertInstanceOf(Banner::class, $result);
    }
}
