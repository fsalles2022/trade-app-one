<?php

namespace Uol\Services;

use Gateway\Services\GatewayService;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\Cancel\CancelService;
use TradeAppOne\Domain\Services\Cancel\ServiceCancel;
use Uol\Models\UolPassport;

class UolCancelService implements ServiceCancel
{
    use CancelService;
    const SEVEN_DAYS = 7;

    private $saleRepository;
    private $gatewayService;
    private $passaporteService;

    public function __construct(
        SaleRepository $saleRepository,
        GatewayService $gatewayService,
        UolPassaporteService $passaporteService
    ) {
        $this->saleRepository    = $saleRepository;
        $this->gatewayService    = $gatewayService;
        $this->passaporteService = $passaporteService;
    }

    public function cancel(User $user, Service $service): ?string
    {
        $this->requirementInDays($service, self::SEVEN_DAYS)
            ->serviceIsApproved($service);

        if ($service->getPaymentStatus() === ServiceStatus::APPROVED) {
            $this->gatewayService->cancel($service);
        }

        if ($id = data_get($service, 'operatorIdentifiers.passportSerie')) {
            $passport = new UolPassport($id);
            $this->passaporteService->cancel($passport);
        }

        $this->saleRepository->updateService($service, ['status' => ServiceStatus::CANCELED]);

        return trans('uol::messages.passport_canceled');
    }
}
