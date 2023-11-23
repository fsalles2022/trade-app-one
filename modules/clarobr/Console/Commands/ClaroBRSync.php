<?php

namespace ClaroBR\Console\Commands;

use Carbon\Carbon;
use ClaroBR\Services\ImportSalesFromClaroService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class ClaroBRSync extends Command
{
    protected $signature   = 'claro:sync {--initial-date=} {--final-date= : Whether the job should be queued} {--interval=}';
    protected $description = 'Bring sales from Siv to TradeAppOne';
    protected $importSalesServiceFromClaro;

    const DATE_FORMAT                = 'Y-m-d';
    const QUANTITY_SALES_PER_REQUEST = 1000;

    public function __construct(ImportSalesFromClaroService $importSalesServiceFromClaro)
    {
        parent::__construct();
        $this->importSalesServiceFromClaro = $importSalesServiceFromClaro;
    }

    public function handle()
    {
        $interval = $this->option('interval');
        if ($initialDateArgument = $this->option('initial-date')) {
            $initialDate = Carbon::createFromFormat('Y-m-d', $initialDateArgument, '+3')->startOfDay();
        }

        if ($finalDateArgument = $this->option('final-date')) {
            $finalDate = Carbon::createFromFormat('Y-m-d', $finalDateArgument, '+3')->endOfDay();
        }

        if (! isset($initialDate)) {
            $initialDate = Carbon::create(2018, 06, 27)->startOfDay();
        }

        if (! isset($finalDate)) {
            $finalDate = Carbon::now();
        }

        if ($interval == 'day') {
            $initialDate = Carbon::now()->startOfDay();
            $finalDate   = Carbon::now()->endOfDay();
        }

        if ($interval == 'week') {
            $initialDate = Carbon::now()->startOfWeek();
            $finalDate   = Carbon::now()->endOfWeek();
        }

        if ($interval == 'month') {
            $initialDate = Carbon::now()->startOfMonth();
            $finalDate   = Carbon::now()->endOfMonth();
        }

        if ($interval == 'all') {
            $initialDate = Carbon::create(2018, 06, 27)->startOfDay();
            $finalDate   = Carbon::now();
        }

        $result = $this->requestSourceAndPaginatePages($initialDate, $finalDate);
        $count  = $result->count();
        $this->output->success("Processo concluído com sucesso, $count vendas atualizadas");
    }

    private function requestSourceAndPaginatePages(?Carbon $initialDate, ?Carbon $finalDate)
    {
        $this->info("Procurando por vendas realizada entre as datas $initialDate a $finalDate");
        $requestInfor = $this->importSalesServiceFromClaro->requestToDescoverTheQuantityOfPages($initialDate, $finalDate);
        $totalOfPages = data_get($requestInfor, 'pages');
        $totalOfSales = data_get($requestInfor, 'total');
        $this->info("Foram encontradas $totalOfSales vendas, essas vendas serão dividadas em $totalOfPages páginas");
        $totalRecordSales = new Collection();
        $this->output->progressStart($totalOfPages);
        for ($currentPage = 1; $currentPage < $totalOfPages; $currentPage++) {
            $period           = [
                'de'             => isset($initialDate) ? $initialDate->format(self::DATE_FORMAT) : '2018-06-27',
                'ate'            => isset($finalDate) ? $finalDate->format(self::DATE_FORMAT) : now()->format(self::DATE_FORMAT),
                'items_per_page' => self::QUANTITY_SALES_PER_REQUEST,
                'page'           => $currentPage
            ];
            $recordSales      = $this->importSalesServiceFromClaro->execute($period);
            $totalRecordSales = $totalRecordSales->merge($recordSales);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
        return $totalRecordSales;
    }
}
