<?php

namespace Reports\Providers;

use Core\PowerBi\Constants\PowerBiDashboards;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Reports\Goals\GoalServiceProvider;
use Reports\SubModules\Hourly\Commands\CeaReportCommand;
use Reports\SubModules\Hourly\Commands\ExtraReportCommand;
use Reports\SubModules\Hourly\Commands\FujiokaReportCommand;
use Reports\SubModules\Hourly\Commands\IplaceReportCommand;
use Reports\SubModules\Hourly\Commands\LebesReportCommand;
use Reports\SubModules\Hourly\Commands\PernambucanasReportCommand;
use Reports\SubModules\Hourly\Commands\SchumannReportCommand;
use Reports\SubModules\Hourly\Commands\TaqiReportCommand;
use Reports\SubModules\Hourly\Constants\HourConstants;
use Reports\SubModules\Hourly\Layout\HourlyLayout;
use Reports\SubModules\Hourly\Commands\TelegramSendDashboardLink;
use Reports\SubModules\Riachuelo\Hourly\Commands\RiachueloHourlyReportCommand;
use Reports\SubModules\Riachuelo\Hourly\Services\HourlyReportService as RiachueloHourlyReportService;

class ReportsProvider extends ServiceProvider
{
    protected $commands = [
        PernambucanasReportCommand::class,
        CeaReportCommand::class,
        //IplaceReportCommand::class,     <-- removed by customer request
        LebesReportCommand::class,
        ExtraReportCommand::class,
        FujiokaReportCommand::class,
        TaqiReportCommand::class,
        SchumannReportCommand::class,
        TelegramSendDashboardLink::class,
        RiachueloHourlyReportCommand::class,
    ];

    public static function schedule(Schedule $schedule): void
    {
        $schedule->command(PernambucanasReportCommand::class)->cron('1 7-23 * * *');
        $schedule->command(CeaReportCommand::class)->cron('1 7-23 * * *');
        //$schedule->command(IplaceReportCommand::class)->cron('1 7-23/3 * * *');     <-- removed by customer request
        $schedule->command(LebesReportCommand::class)->cron('1 7-23 * * *');
        $schedule->command(ExtraReportCommand::class, ['--exclude' => HourlyLayout::PRE_PAGO])->cron('1 7-23 * * *');
        $schedule->command(TaqiReportCommand::class)->cron('1 7-23 * * *');
        $schedule->command(SchumannReportCommand::class, ['--exclude' => HourConstants::VALUES])->cron('1 7-23 * * *');
        $schedule->command(FujiokaReportCommand::class, ['--exclude' => HourlyLayout::PRE_PAGO,])->cron('1 7-23 * * *');
        $schedule->command(RiachueloHourlyReportCommand::class)->cron('1 7-23 * * *');
        $schedule->command(TelegramSendDashboardLink::class, [
            '--dashboard' => PowerBiDashboards::MANAGEMENT[PowerBiDashboards::NAME]
        ])
            ->cron('0 8-22 * * *')
            ->withoutOverlapping();
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/reports.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->loadTranslationsFrom(__DIR__ . '/../translations/', 'reports');
        (new GoalServiceProvider($this->app))->boot();
    }

    public function register(): void
    {
        $this->commands($this->commands);

        $this->app->bind(RiachueloHourlyReportService::class);
    }
}
