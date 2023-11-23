<?php

namespace Reports\SubModules\Hourly\Commands;

use Illuminate\Console\Command;
use Reports\SubModules\Hourly\HourlyReportJob;
use TradeAppOne\Domain\Enumerators\NetworkEnum;

class TaqiReportCommand extends Command
{
    protected $signature   = 'hourly:taqi {--date= : Date of sync, example 2018-12-12-10-10}';
    protected $description = 'Send email with report hourly';

    public function handle()
    {
        $allOptions = array_merge($this->options(), [
            'network' => NetworkEnum::TAQI,
            'chatId'  => 'telegram.taqi',
//            'mailTo'  => NetworkEmails::TAQI,
//            'mailCC'  => NetworkEmails::TAQI_CC
        ]);
        $report = new HourlyReportJob($allOptions);
        $report->handle();
    }
}
