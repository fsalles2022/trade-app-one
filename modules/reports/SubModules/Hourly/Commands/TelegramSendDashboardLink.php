<?php

namespace Reports\SubModules\Hourly\Commands;

use Illuminate\Console\Command;
use Reports\SubModules\Hourly\Services\TelegramDashboardLinkService;
use TradeAppOne\Domain\Enumerators\NetworkEnum;

class TelegramSendDashboardLink extends Command
{
    protected $signature   = 'hourly:dash-link {--dashboard=}';
    protected $description = 'Send dashboard link hourly to telegram';

    private const NETWORKS_ABLE_TO_SEND = [
        NetworkEnum::RIACHUELO,
        'notify-channel'
    ];

    public function handle(TelegramDashboardLinkService $telegramDashboardLinkService)
    {
        $dashboardName = $this->option('dashboard');
        foreach (self::NETWORKS_ABLE_TO_SEND as $network) {
            $telegramDashboardLinkService->sendDashboardLink($dashboardName, $network);
        }
    }
}
