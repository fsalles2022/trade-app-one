<?php

declare(strict_types=1);

namespace Reports\SubModules\Riachuelo\Hourly\Commands;

use Illuminate\Console\Command;
use Reports\SubModules\Hourly\Constants\ReportTypeConstants;
use Reports\SubModules\Riachuelo\Hourly\Services\HourlyReportService;

class RiachueloHourlyReportCommand extends Command
{
    /** @var string */
    protected $signature = 'hourly:riachuelo';

    /** @var string */
    protected $description = 'Send email with report hourly';

    /** @var HourlyReportService */
    protected $hourlyReportService;

    public function __construct(HourlyReportService $hourlyReportService)
    {
        $this->hourlyReportService = $hourlyReportService;

        parent::__construct();
    }

    public function handle(): void
    {
        $this->hourlyReportService->report(ReportTypeConstants::DAILY);
    }
}
