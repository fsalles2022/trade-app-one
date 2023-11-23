<?php

namespace TradeAppOne\Tests\Unit\Console\Commands;

use Mockery;
use Illuminate\Support\Facades\Artisan;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\NetworkService;
use TradeAppOne\Console\Commands\AddSourceInSavedSales;
use TradeAppOne\Exceptions\BusinessExceptions\NetworkNotFoundException;
use TradeAppOne\Tests\TestCase;

class AddSourceInSavedSalesTest extends TestCase
{
    /** @test */
    public function should_be_registred()
    {
        $saleRepository = Mockery::mock(SaleRepository::class)->makePartial();

        $networkService = Mockery::mock(NetworkService::class)->makePartial();
        $networkService->shouldReceive('findOneBySlug')->once();

        app()->singleton(AddSourceInSavedSales::class, function() use ($saleRepository, $networkService) {
            return new AddSourceInSavedSales($saleRepository, $networkService);
        });

        Artisan::call('sale:sync-source', ['--network' => 'cea']);
    }

    /** @test */
    public function should_return_exception_when_network_invalid()
    {
        $this->expectException(NetworkNotFoundException::class);
        Artisan::call('sale:sync-source', ['--network' => 'invalid']);
    }
}
