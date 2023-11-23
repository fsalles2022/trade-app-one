<?php

declare(strict_types=1);

namespace Reports\SubModules\Hourly\Commands;

use Illuminate\Console\Command;
use Reports\SubModules\Hourly\HourlyReportJob;
use TradeAppOne\Domain\Enumerators\NetworkEnum;

class RiachueloReportCommand extends Command
{
    protected $signature   = 'hourly:riachuelo {--date= : Date of sync, example 2018-12-12-10-10}';
    protected $description = 'Send email with report hourly';

    public function handle(): void
    {
        $allOptions = array_merge($this->options(), [
            'network' => NetworkEnum::RIACHUELO,
            'chatId'  => 'telegram.riachuelo',
        ]);
        $report     = new HourlyReportJob($allOptions);
        $report->handle();
    }
}
