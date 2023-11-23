<?php

namespace TradeAppOne\Console;

use Buyback\Console\TradeInCancelServiceCommand;
use ClaroBR\Console\Commands\ClaroBRUpdateMsisdnCommand;
use TradeAppOne\Console\Commands\TimBRCommissioningCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use McAfee\Console\McAfeeTrialCommand;
use Outsourced\GPA\Commands\GPASentinel;
use Outsourced\ViaVarejo\Console\ViaVarejoSentinel;
use Reports\Providers\ReportsProvider;
use TradeAppOne\Console\Commands\ClaroPromotionsUpdateCommand;
use TradeAppOne\Console\Commands\HealthSalesReport;
use TradeAppOne\Console\Commands\MailingNegados;
use TradeAppOne\Console\Commands\Registration\RegisterMailToOiCommand;
use TradeAppOne\Console\Commands\SalesImeiUnlink;
use TradeAppOne\Console\Commands\SalesSentinelOiBR;
use TradeAppOne\Console\Commands\SalesSentinelSiv;
use TradeAppOne\Console\Commands\SalesSentinelTim;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $quick = now()->subHours(8)->format('Y-m-d-h-m');
//        $schedule->command(TradeInCancelServiceCommand::class)->dailyAt('02:00');

        $schedule->command('queue:work --queue=default,ACTIONS_LOG --once')->everyMinute()->withoutOverlapping();
        $schedule->command(SalesSentinelOiBR::class)->everyFifteenMinutes();

        $schedule->command(SalesSentinelSiv::class, ['--initial-date' => $quick])->everyFiveMinutes()->withoutOverlapping();
        $schedule->command(SalesSentinelSiv::class)->at('01:00');

        $schedule->command(SalesSentinelTim::class, ['--daily'])->everyFifteenMinutes()->withoutOverlapping();
        $schedule->command(SalesSentinelTim::class, ['--yearly'])->cron('0 */8 * * *')->withoutOverlapping();
        $schedule->command(SalesSentinelTim::class, ['--all'])->sundays()->at('00:30')->withoutOverlapping();

        $schedule->command(HealthSalesReport::class)->everyTenMinutes();

        $schedule->command(TimBRCommissioningCommand::class)
            ->dailyAt('00:00:01')
            ->withoutOverlapping();

        $schedule->command(RegisterMailToOiCommand::class, ['--all'])->mondays()->at('01:00');
        $schedule->command(RegisterMailToOiCommand::class, ['--all'])->wednesdays()->at('01:00');

        $schedule->command(ClaroPromotionsUpdateCommand::class)->twiceDaily(6, 12);

        $schedule->command(MailingNegados::class)->hourly();
        $schedule->command(SalesImeiUnlink::class)->everyFifteenMinutes();

        $schedule->command(McAfeeTrialCommand::class)->dailyAt('00:00:01');
        $schedule->command(ViaVarejoSentinel::class)->hourly();
        $schedule->command(GPASentinel::class)->everyFiveMinutes();
        $schedule->command(ClaroBRUpdateMsisdnCommand::class, ['--initial' => '01/01/2021'])
            ->hourly()
            ->withoutOverlapping();

            ReportsProvider::schedule($schedule);
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
