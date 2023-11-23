<?php

namespace TradeAppOne\Tests\Unit\Console\Commands;

use Carbon\Carbon;
use ClaroBR\Tests\Helpers\ClaroServices;
use Illuminate\Support\Facades\Artisan;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class AddHierarchyToSavedSalesTest extends TestCase
{
    /** @test */
    public function should_allow_network_and_initial_date_and_final_date_as_parameter()
    {
        Artisan::call('sale:sync-hierarchy', [
            '--network' => NetworkEnum::CEA,
            '--initial-date' => Carbon::yesterday()->format('Y-m-d-H-i'),
            '--final-date' => Carbon::now()->format('Y-m-d-H-i'),
        ]);
    }

    /** @test */
    public function should_allow_only_network_as_parameter()
    {
        $hierarchy = (new HierarchyBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withHierarchy($hierarchy)->build();
        (new SaleBuilder())
            ->withPointOfSale($pointOfSale)
            ->withServices([ClaroServices::ControleBoleto()])
            ->build();
        Artisan::call('sale:sync-hierarchy', [
            '--network' => $pointOfSale->network->slug,
        ]);
    }
}
