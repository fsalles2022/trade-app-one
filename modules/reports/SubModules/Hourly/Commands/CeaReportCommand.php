<?php

namespace Reports\SubModules\Hourly\Commands;

use Illuminate\Console\Command;
use Reports\SubModules\Hourly\HourlyReportJob;
use TradeAppOne\Domain\Enumerators\NetworkEnum;

class CeaReportCommand extends Command
{
    protected $signature   = 'hourly:cea {--date= : Date of sync, example 2018-12-12-10-10}';
    protected $description = 'Send email with report hourly';

    public function handle()
    {
        $allOptions = array_merge($this->options(), [
            'network' => NetworkEnum::CEA,
            'chatId'  => 'telegram.cea',
//            'mailTo'  => NetworkEmails::CEA,
//            'mailCC'  => NetworkEmails::CEA_CC
        ]);
        $report = new HourlyReportJob($allOptions);
        $report->handle();
    }
}
