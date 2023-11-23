<?php

namespace Reports\SubModules\Hourly\Commands;

use Illuminate\Console\Command;
use Illuminate\Validation\Rule;
use Reports\SubModules\Hourly\Constants\HourConstants;
use Reports\SubModules\Hourly\HourlyReportJob;
use Reports\SubModules\Hourly\Layout\HourlyLayout;
use TradeAppOne\Domain\Components\Console\OptionsValidator;
use TradeAppOne\Domain\Enumerators\NetworkEnum;

class ExtraReportCommand extends Command
{
    protected $signature   = 'hourly:extra {--date= : Date of sync, example 2018-12-12-10-10} {--exclude=*}';
    protected $description = 'Send email with report hourly';

    public function handle()
    {
        OptionsValidator::validate($this->option(), [
            'exclude' => Rule::in([
                HourlyLayout::PRE_PAGO,
                HourlyLayout::POS_PAGO,
                HourConstants::VALUES
            ])
        ]);

        $allOptions = array_merge($this->options(), [
            'network' => NetworkEnum::EXTRA,
            'chatId' => 'telegram.extra'
        ]);

        $report = new HourlyReportJob($allOptions);

        $report->handle();
    }
}
