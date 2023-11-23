<?php

namespace Reports\SubModules\Hourly\Commands;

use Illuminate\Console\Command;
use Reports\Enum\NetworkEmails;
use Reports\SubModules\Hourly\HourlyReportJob;
use TradeAppOne\Domain\Enumerators\NetworkEnum;

class LebesReportCommand extends Command
{
    protected $signature   = 'hourly:lebes {--date= : Date of sync, example 2018-12-12-10-10}';
    protected $description = 'Send email with report hourly';

    public function handle()
    {
        $allOptions = array_merge($this->options(), [
            'network' => NetworkEnum::LEBES,
            'chatId'  => 'telegram.lebes',
            'mailTo'  => NetworkEmails::LEBES,
            'mailCC'  => NetworkEmails::LEBES_CC
        ]);
        $report     = new HourlyReportJob($allOptions);
        $report->handle();
    }
}
