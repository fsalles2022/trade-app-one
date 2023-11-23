<?php

namespace TradeAppOne\Console\Commands;

use Illuminate\Console\Command;
use TimBR\Services\TimBRSentinel;

class SalesSentinelTim extends Command
{
    protected $signature = 'sentinel:tim {--daily} {--yearly} {--all} {--explained}';
    protected $sentinel;

    protected $description = 'Command description';

    public function __construct(TimBRSentinel $sentinel)
    {
        $this->sentinel = $sentinel;
        parent::__construct();
    }

    public function handle(): void
    {
        $options = $this->options();

        $explainedMode = data_get($options, 'explained', false);
        $this->sentinel->setExplainedMode($explainedMode);

        $isDailyCommand = data_get($options, 'daily', false);
        if ($isDailyCommand) {
            $this->sentinel->sentinelDailySalesByProtocol();
        }

        $isYearlyCommand = data_get($options, 'yearly', false);
        if ($isYearlyCommand) {
            $this->sentinel->sentinelYearlySalesByProtocol();
        }

        $isAllCommand = data_get($options, 'all', false);
        if ($isAllCommand) {
            $this->sentinel->sentinelGetAllSalesByProtocol();
        }
    }
}
