<?php

namespace Reports\SubModules\Hourly\Commands;

use Illuminate\Console\Command;
use Reports\Enum\NetworkEmails;
use Reports\SubModules\Hourly\HourlyReportJob;
use TradeAppOne\Domain\Enumerators\NetworkEnum;

class IplaceReportCommand extends Command
{
    protected $signature   = 'hourly:iplace {--date= : Date of sync, example 2018-12-12-10-10}';
    protected $description = 'Send email with report hourly';

    public function handle()
    {
        $allOptions = array_merge($this->options(), [
            'network' => NetworkEnum::IPLACE,
            'chatId'  => 'telegram.iplace',
            'mailTo'  => NetworkEmails::IPLACE,
            'mailCC'  => NetworkEmails::IPLACE_CC
        ]);

        $report = new HourlyReportJob($allOptions);
        $report->handle();
    }
}
