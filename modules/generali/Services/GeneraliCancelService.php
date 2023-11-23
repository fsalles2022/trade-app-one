<?php


namespace Generali\Services;

use Gateway\Services\GatewayService;
use Generali\Assistance\Connection\GeneraliConnection;
use Generali\Exceptions\GeneraliExceptions;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\Cancel\CancelService;
use TradeAppOne\Domain\Services\Cancel\ServiceCancel;

class GeneraliCancelService implements ServiceCancel
{
    use CancelService;

    protected $gatewayService;
    protected $saleRepository;
    protected $generaliConnection;

    public function __construct(
        GatewayService $gatewayService,
        SaleRepository $saleRepository,
        GeneraliConnection $generaliConnection
    ) {
        $this->gatewayService     = $gatewayService;
        $this->saleRepository     = $saleRepository;
        $this->generaliConnection = $generaliConnection;
    }

    public function cancel(User $user, Service $service): string
    {
        $this->serviceIsApproved($service);

        if ($service->getPaymentStatus() === ServiceStatus::APPROVED) {
            $refund = $this->generaliConnection->calcRefund($service->serviceTransaction)->get('data.refund.value');
            $value  = bcmul($refund, 100, 0);
            $this->gatewayService->cancel($service, $value);
        }

        $response = $this->generaliConnection->cancel($service->serviceTransaction);

        if ($response->getStatus() === Response::HTTP_OK) {
            $this->saleRepository->updateService($service, ['status' => ServiceStatus::CANCELED]);
            return trans('generali::messages.service_cancelled');
        }

        throw GeneraliExceptions::serviceNotCancelled();
    }
}
