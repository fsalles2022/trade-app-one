<?php

namespace TradeAppOne\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use TradeAppOne\Domain\Services\PointOfSaleService;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Http\Resources\PointOfSaleResource;

class AddHierarchyToSavedSales extends Command
{
    protected $signature   = 'sale:sync-hierarchy {--network= : The network slug} {--initial-date=} {--final-date=}';
    protected $description = 'Search by sales network and synchronizes the hierarchy information';
    protected $saleService;
    protected $pointOfSaleService;

    public function __construct(SaleService $saleService, PointOfSaleService $pointOfSaleService)
    {
        parent::__construct();
        $this->saleService        = $saleService;
        $this->pointOfSaleService = $pointOfSaleService;
    }

    public function handle()
    {
        $networkSlug = $this->option('network');
        if ($initialDate = $this->option('initial-date')) {
            $initialDate = Carbon::createFromFormat('Y-m-d-H-i', $initialDate);
        }
        if ($finalDate = $this->option('final-date')) {
            $finalDate = Carbon::createFromFormat('Y-m-d-H-i', $finalDate);
        }
        $sales        = $this->saleService
            ->getByNetworkSlug($networkSlug, array_filter(compact('initialDate', 'finalDate')));
        $salesUpdated = 0;

        foreach ($sales as $sale) {
            $pointOfSaleId = data_get($sale, 'pointOfSale.id');
            $pointOfSale   = $this->pointOfSaleService->find($pointOfSaleId);

            if (is_object($pointOfSale)) {
                $correctPointOfSale = (new PointOfSaleResource())->map($pointOfSale);
                $this->saleService->updateSale($sale, ['pointOfSale' => $correctPointOfSale]);
                $salesUpdated++;
            }
        }

        $this->info("${salesUpdated} updated");
    }
}
