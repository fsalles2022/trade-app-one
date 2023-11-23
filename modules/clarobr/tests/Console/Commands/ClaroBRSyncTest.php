<?php

namespace ClaroBR\Tests\Console\Commands;

use ClaroBR\Console\Commands\ClaroBRSync;
use ClaroBR\Services\ImportSalesFromClaroService;
use Illuminate\Support\Facades\Artisan;
use TradeAppOne\Tests\TestCase;

class ClaroBRSyncTest extends TestCase
{
    /** @test */
    public function should_call_execute_of_import_command()
    {
        $importSalesServiceFromClaro = \Mockery::mock(ImportSalesFromClaroService::class)->makePartial();
        $importSalesServiceFromClaro->shouldReceive('requestToDescoverTheQuantityOfPages')
                ->withAnyArgs()
                ->once()
                ->andReturn(['pages' => 1, 'total' => 1]);

        $this->app->singleton(ClaroBRSync::class, function () use ($importSalesServiceFromClaro) {
            return new ClaroBRSync($importSalesServiceFromClaro);
        });

        Artisan::call('claro:sync');
    }
}
