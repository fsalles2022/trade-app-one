<?php

namespace Reports\SubModules\Hourly\Commands;

use Illuminate\Console\Command;
use Reports\SubModules\Hourly\HourlyReportJob;
use TradeAppOne\Domain\Enumerators\NetworkEnum;

class PernambucanasReportCommand extends Command
{
    protected $signature   = 'hourly:pernambucanas {--date= : Date of sync, example 2018-12-12-10-10}';
    protected $description = 'Send email with report hourly';

    public function handle()
    {
        $allOptions = array_merge($this->options(), [
            'network' => NetworkEnum::PERNAMBUCANAS,
            'chatId'  => 'telegram.pernambucanas',
//            'mailTo'  => NetworkEmails::PERNAMBUCANAS,
//            'mailCC'  => NetworkEmails::PERNAMBUCANAS_CC
        ]);
        $report = new HourlyReportJob($allOptions);
        $report->handle();
    }
}
