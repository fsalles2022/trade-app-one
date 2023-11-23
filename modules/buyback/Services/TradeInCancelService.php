<?php

namespace Buyback\Services;

use Buyback\Assistance\TradeInSaleAssistance;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\Cancel\CancelService;
use TradeAppOne\Domain\Services\Cancel\ServiceCancel;

class TradeInCancelService implements ServiceCancel
{
    use CancelService;

    private $assistance;

    public function __construct(TradeInSaleAssistance $assistance)
    {
        $this->assistance = $assistance;
    }

    public function cancel(User $user, Service $service): ?string
    {
        $this->validateService($service);

        $this->assistance
            ->setStatusVoucher($service, $service->serviceTransaction, ServiceStatus::CANCELED);

        return trans('buyback::messages.voucher_canceled');
    }

    private function validateService(Service $service): void
    {
        $this->serviceNotNull($service)
            ->serviceNotCanceled($service)
            ->serviceIsAccepted($service);
    }
}
