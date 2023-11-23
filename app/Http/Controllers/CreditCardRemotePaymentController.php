<?php

declare(strict_types=1);

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Adapters\RemotePaymentCreditCardResponseAdapter;
use TradeAppOne\Domain\Services\RemotePaymentServices;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Exceptions\BusinessExceptions\BusinessRuleExceptions;
use TradeAppOne\Exceptions\BusinessExceptions\ServiceInvalidException;
use TradeAppOne\Http\Requests\ActivationFormRequest;
use TradeAppOne\Domain\Enumerators\Operations;

class CreditCardRemotePaymentController extends Controller
{
    /** @var RemotePaymentServices */
    private $remotePaymentServices;

    /** @var SaleService */
    private $saleService;

    public function __construct(
        RemotePaymentServices $remotePaymentServices,
        SaleService $saleService
    ) {
        $this->remotePaymentServices = $remotePaymentServices;
        $this->saleService           = $saleService;
    }

    /** @throws \Throwable */
    public function getService(string $token): JsonResponse
    {
        $service = $this->remotePaymentServices->findServicesByToken($token);

        if (empty($service)) {
            return response()->json(['error' => true], Response::HTTP_NOT_FOUND);
        }

        $adaptedService = RemotePaymentCreditCardResponseAdapter::adaptToCreditCard($service);
        return response()->json(['service' => $adaptedService], Response::HTTP_OK);
    }

    public function putActivate(ActivationFormRequest $request): JsonResponse
    {
        try {
            $service = $this->saleService->findService($request->get('serviceTransaction'));

            if (! in_array($service->operator, [Operations::MCAFEE, Operations::GENERALI])) {
                throw new ServiceInvalidException();
            }

            $activation = $this->saleService->integrateService($request->all());

            return is_array($activation) ? response()->json($activation, Response::HTTP_OK) : $activation;
        } catch (BusinessRuleExceptions $e) {
            $this->response['error'] = $e->getError();

            return response()->json($this->response, $e->getHttpStatus());
        }
    }
}
