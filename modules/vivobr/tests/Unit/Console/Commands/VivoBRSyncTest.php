<?php

namespace VivoBR\Tests\Unit\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use TradeAppOne\Tests\TestCase;
use VivoBR\Console\Commands\VivoBRSync;
use VivoBR\Services\ImportSalesFromVivoService;
use VivoBR\Tests\Helpers\VivoFactoriesHelper;
use VivoBR\Tests\Unit\Console\KernelTest;

class VivoBRSyncTest extends TestCase
{
    use VivoFactoriesHelper;

    /** @test */
    public function should_call_execute_of_import_command()
    {
        $importSalesServiceFromVivo = \Mockery::mock(ImportSalesFromVivoService::class)->makePartial();
        $importSalesServiceFromVivo->shouldReceive('execute')->withAnyArgs()->once()->andReturn(collect());
        $this->app->singleton(VivoBRSync::class, function () use ($importSalesServiceFromVivo) {
            return new VivoBRSync($importSalesServiceFromVivo);
        });

        Artisan::call('vivo:sync');
    }

    protected function setUp()
    {
        parent::setUp();
        $this->app->singleton('Illuminate\Contracts\Console\Kernel', KernelTest::class);
    }
}
