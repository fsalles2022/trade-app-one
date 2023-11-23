<?php


namespace TradeAppOne\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Services\SaleService;

class SalesImeiUnlink extends Command
{
    protected $signature   = 'sales:imei-unlink {--network= : Network slug to unlink imeis from sales}';
    protected $description = 'Unlinks IMEI from unsuccessful network sales';
    protected $saleService;
    protected $actualDateSpan;
    protected $initialDate;

    public function __construct(SaleService $saleService)
    {
        parent::__construct();
        $this->saleService    = $saleService;
        $this->actualDateSpan = Carbon::now()->subMinutes(60);
        $this->initialDate    = $this->actualDateSpan->subDay();
    }

    public function handle(): void
    {
        $networkSlug   = $this->option('network') ?? 'riachuelo';
        $salesToUnlink = $this->searchSales($networkSlug);
        $this->unlinkImeis($salesToUnlink);
    }

    private function searchSales(string $slug): Collection
    {
        return Sale::where('pointOfSale.network.slug', '=', $slug)
            ->whereIn('services.status', [ServiceStatus::CANCELED, ServiceStatus::REJECTED])
            ->whereNotNull('services.imei')
            ->where('services.imei', '<>', '')
            ->whereBetween('createdAt', [$this->initialDate, $this->actualDateSpan])
            ->limit(1000)
            ->get();
    }

    private function unlinkImeis(Collection $sales): void
    {
        $sales->each(
            function ($sale) {
                $service = $sale->services->where('imei', '!=', '')->first();
                if ($service) {
                    $attributes = ['imei' => '', 'imeiLog' => data_get($service, 'imei')];
                    $this->saleService->updateService($service, $attributes);
                }
            }
        );
    }
}
