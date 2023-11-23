<?php


namespace Reports\SubModules\Hourly\Services;

use Outsourced\Partner\Constants\PartnerConstants;
use TradeAppOne\Domain\Components\Telegram\Telegram;

class TelegramDashboardLinkService
{
    /** @var Telegram */
    private $telegramClient;

    private $availableDash = [
        'management' => [
            'link' => '?redirect=dashboard/management',
            'message' => 'Olá! Já checou seu painel gerencial hoje? Acesse o link e saiba como andam suas vendas'
        ]
    ];

    public function __construct(Telegram $telegram)
    {
            $this->telegramClient = $telegram;
    }

    public function sendDashboardLink(string $dashboardName, string $networkSlug = null): void
    {
        if ($this->checkDashboardExists($dashboardName)) {
            $message = $this->mountMessage($dashboardName);
            $this->telegramClient->sendMessage([
                'chat_id' => config('telegram.'.$networkSlug) ?? config('telegram.notify-channel'),
                'text' => $message,
                'caption' => 'Dashboard Notification - Trade App One!'
            ]);
        }
    }

    private function checkDashboardExists(string $dashboardName): bool
    {
        if (array_key_exists($dashboardName, $this->availableDash)) {
            return true;
        }
        return false;
    }

    private function mountMessage(string $dashboardName): string
    {
        $dashboard = data_get($this->availableDash, $dashboardName, []);
        $domain    = app()->environment() === 'production' ? PartnerConstants::FRONT_END_URL : 'http://localhost:8080/';

        return data_get($dashboard, 'message', '')
            . ' - '
            . $domain
            . data_get($dashboard, 'link', '');
    }
}
