<?php

namespace Uol\Assistance;

use Gateway\Services\GatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\AssistanceBehavior;
use TradeAppOne\Domain\Services\SaleService;
use Uol\Adapters\Response\UolSaleAssistanceResponseAdapter;
use Uol\Exceptions\UolExceptions;
use Uol\Mail\CoursePurchased;
use Uol\Services\UolPassaporteService;

class UolSaleAssistance implements AssistanceBehavior
{
    private $uolPassaporteService;
    private $gatewayService;
    private $saleService;

    public function __construct(
        UolPassaporteService $uolService,
        GatewayService $gatewayService,
        SaleService $saleService
    ) {
        $this->uolPassaporteService = $uolService;
        $this->gatewayService       = $gatewayService;
        $this->saleService          = $saleService;
    }

    public function integrateService(Service $service, array $payload = []): JsonResponse
    {
        $creditCard = data_get($payload, 'creditCard');

        $serviceTokenized = $this->gatewayService->tokenize($service, $creditCard);
        $this->gatewayService->sale($serviceTokenized);

        $passport = $this->uolPassaporteService->generate($service->product);

        if ($passport->isNotConfirmed()) {
            $this->gatewayService->cancel($service);
            throw UolExceptions::uolErrorGeneratingPassport();
        }

        $update = $this->saleService->updateService($service, [
            'operatorIdentifiers' => [
                'passportNumber' => $passport->number,
                'passportSerie'  => $passport->id,
            ],
            'status' => ServiceStatus::APPROVED
        ]);

        Mail::to($service->customer['email'])->send(new CoursePurchased($update, $passport));

        return response()->json(
            UolSaleAssistanceResponseAdapter::adapt($passport),
            Response::HTTP_OK
        );
    }
}
