<?php

namespace VivoBR\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use VivoBR\Services\ImportSalesFromVivoService;

//TODO - INTEGRACAO DESATIVADA.

class VivoBRSync extends Command
{
    const DATE_FORMAT      = 'Y-m-d-h-i';
    protected $signature   = 'vivo:sync {--initial-date=} {--final-date= : Whether the job should be queued} {--network=} {--interval=}';
    protected $description = 'Bring sales from Sun to TradeAppOne';
    protected $importSalesServiceFromVivo;

    public function __construct(ImportSalesFromVivoService $importSalesServiceFromVivo)
    {
        parent::__construct();
        $this->importSalesServiceFromVivo = $importSalesServiceFromVivo;
    }

    public function handle()
    {
        $interval = $this->option('interval');
        if ($initialDateArgument = $this->option('initial-date')) {
            $initialDate = Carbon::createFromFormat('Y-m-d-H-i', $initialDateArgument, '+3');
        }

        if ($finalDateArgument = $this->option('final-date')) {
            $finalDate = Carbon::createFromFormat('Y-m-d-H-i', $finalDateArgument, '+3');
        }
        if ($interval == 'hour') {
            $initialDate = Carbon::now()->startOfHour();
            $finalDate   = Carbon::now()->endOfHour();
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
            $initialDate = Carbon::create(2018, 06, 27);
            $finalDate   = Carbon::now();
        }
        $period  = compact('initialDate', 'finalDate');
        $network = $this->option('network');
        $options = array_merge($period, compact('network'));
        $result  = $this->importSalesServiceFromVivo->execute($options);
        $this->info($result->count() . ' records');
        $this->info($result->where('action', 'update')->count() . ' updated');
        $this->info($result->where('action', 'create')->count() . ' created');
    }
}
