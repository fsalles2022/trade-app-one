<?php


namespace McAfee\Services;

use Gateway\Services\GatewayService;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\Cancel\CancelService;
use TradeAppOne\Domain\Services\Cancel\ServiceCancel;
use TradeAppOne\Domain\Services\SaleService;

class McAfeeCancelService implements ServiceCancel
{
    use CancelService;

    private $gatewayService;
    private $saleService;
    private $mcAfeeService;

    public function __construct(GatewayService $gatewayService, SaleService $saleService, McAfeeService $mcAfeeService)
    {
        $this->gatewayService = $gatewayService;
        $this->saleService    = $saleService;
        $this->mcAfeeService  = $mcAfeeService;
    }

    public function cancel(User $user, Service $service): ?string
    {
        $this->serviceIsApproved($service);

        if ($service->statusThirdParty === ServiceStatus::APPROVED) {
            $this->mcAfeeService->cancelSubscription($service, $user);
            $this->mcAfeeService->disconnectDevices($service, $user);
        }

        if ($service->getPaymentStatus() === ServiceStatus::APPROVED) {
            $this->gatewayService->cancel($service);
        }

        $service = $this->saleService->updateStatusService($service, ServiceStatus::CANCELED);
        return trans('mcAfee::messages.subscription.canceled', ['label' => $service->label]);
    }
}
