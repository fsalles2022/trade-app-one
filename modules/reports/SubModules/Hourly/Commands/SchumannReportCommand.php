<?php

namespace Reports\SubModules\Hourly\Commands;

use Illuminate\Console\Command;
use Reports\SubModules\Hourly\HourlyReportJob;
use TradeAppOne\Domain\Enumerators\NetworkEnum;

class SchumannReportCommand extends Command
{
    protected $signature   = 'hourly:schumann {--date= : Date of sync, example 2018-12-12-10-10} {--exclude=*}';
    protected $description = 'Send email with report hourly';

    public function handle()
    {
        $allOptions = array_merge($this->options(), [
            'network' => NetworkEnum::SCHUMANN,
            'chatId'  => 'telegram.schumann',
        ]);
        $report     = new HourlyReportJob($allOptions);
        $report->handle();
    }
}
