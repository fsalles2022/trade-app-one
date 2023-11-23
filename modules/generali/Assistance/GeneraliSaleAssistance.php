<?php

namespace Generali\Assistance;

use Gateway\Services\GatewayService;
use Generali\Assistance\Connection\GeneraliConnection;
use Illuminate\Http\JsonResponse;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\AssistanceBehavior;
use TradeAppOne\Domain\Services\SaleService;

class GeneraliSaleAssistance implements AssistanceBehavior
{
    protected $generaliConnection;
    protected $gatewayService;
    protected $saleService;

    public function __construct(
        GeneraliConnection $generaliConnection,
        GatewayService $gatewayService,
        SaleService $saleService
    ) {
        $this->generaliConnection = $generaliConnection;
        $this->gatewayService     = $gatewayService;
        $this->saleService        = $saleService;
    }

    public function integrateService(Service $service, array $payload = []): JsonResponse
    {
        $this->creditPayment($payload, $service);

        try {
            return $this->generaliConnection->activate($service);
        } catch (\Exception $exception) {
            $this->gatewayService->cancel($service);
            throw $exception;
        }
    }

    private function creditPayment(array $payload, Service $service): void
    {
        $creditCard                   = data_get($payload, 'creditCard');
        $creditCard['softDescriptor'] = Operations::GENERALI;
        $numberOfPayments             = data_get($creditCard, 'times');

        $this->gatewayService->tokenize($service, $creditCard);
        $this->gatewayService->sale($service, $numberOfPayments);
    }
}
