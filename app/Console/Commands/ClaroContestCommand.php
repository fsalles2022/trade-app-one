<?php

namespace TradeAppOne\Console\Commands;

use ClaroBR\Services\ClaroBRContest;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;

class ClaroContestCommand extends Command
{
    protected $signature   = 'contest:claro {--networks=*} {--initial-date=} {--final-date=}';
    protected $description = 'Verifica no SIV por vendas com status divergentes e realiza a sincronização.';

    private $claroContest;

    public function __construct(ClaroBRContest $claroContest)
    {
        parent::__construct();
        $this->claroContest = $claroContest;
    }

    public function handle()
    {
        $salesBuilder           = Sale::query();
        $invalidStatusToContest = [ServiceStatus::APPROVED, ServiceStatus::ACCEPTED, ServiceStatus::PENDING_SUBMISSION];
        $salesBuilder->whereNotIn('services.status', $invalidStatusToContest);
        $this->applyFilter($salesBuilder);

        $sales                 = $salesBuilder->get();
        $verifiedSales         = 0;
        $salesWithErorUpdating = 0;
        foreach ($sales as $sale) {
            foreach ($sale->services as $service) {
                try {
                    $this->claroContest->contestService($service);
                    $verifiedSales++;
                } catch (\Exception $exception) {
                    $salesWithErorUpdating++;
                }
            }
        }

        $salesAmount = $sales->count();
        $this->info("Sales amount: $salesAmount");
        $this->info("Verified sales: $verifiedSales");
        $this->info("Invalid response: $salesWithErorUpdating");
    }

    private function applyFilter(Builder &$salesBuilder)
    {
        ['networks' => $networks,'initial-date' => $initialDate, 'final-date' => $finalDate] = $this->options();

        if ($networks) {
            $salesBuilder->whereIn('pointOfSale.network.slug', $networks);
        }
        if ($initialDate) {
            $salesBuilder->where('created_at', '>', $initialDate);
        }

        if ($finalDate) {
            $salesBuilder->where('created_at', '<', $finalDate);
        }
    }
}
