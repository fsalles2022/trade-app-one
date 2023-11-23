<?php

namespace TradeAppOne\Console\Commands;

use Illuminate\Console\Command;
use TradeAppOne\Domain\Enumerators\SubSystemEnum;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\NetworkService;
use TradeAppOne\Exceptions\BusinessExceptions\SaleNotFoundException;

class AddSourceInSavedSales extends Command
{
    protected $signature   = 'sale:sync-source {--network= : The network slug}';
    protected $description = 'Adds web source to sales that have no source.';

    protected $saleRepository;
    protected $networkService;

    public function __construct(SaleRepository $saleRepository, NetworkService $networkService)
    {
        parent::__construct();
        $this->saleRepository = $saleRepository;
        $this->networkService = $networkService;
    }

    public function handle()
    {
        $networkSlug  = $this->option('network');
        $salesUpdated = $this->addSourceToSale($networkSlug);

        $this->info($salesUpdated." updated");
    }

    private function addSourceToSale(string $networkSlug): int
    {
        $salesUpdated = 0;
        $this->networkService->findOneBySlug($networkSlug);

        $sales = $this->saleRepository->where('pointOfSale.network.slug', $networkSlug)->get();
        throw_if(! $sales, new SaleNotFoundException());
        foreach ($sales as $sale) {
            $source = data_get($sale, 'source');
            if (! $source) {
                $this->saleRepository->updateSale($sale, ['source' => SubSystemEnum::WEB]);
                $salesUpdated++;
            }
        }
        return $salesUpdated;
    }
}
