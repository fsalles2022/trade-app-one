<?php

namespace OiBR\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OiBR\Assistance\OiBRService;
use OiBR\Http\Requests\OiBREligibilityFormRequest;
use OiBR\Http\Requests\OiBRPlansFormRequest;
use OiBR\Http\Requests\OiBRREgisterCreditCardFormRequest;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Http\Controllers\Controller;

class OiBRController extends Controller
{
    protected $service;

    public function __construct(OiBRService $service)
    {
        $this->service = $service;
    }

    public function getCreditCardsOfMsisdn(Request $request): JsonResponse
    {
        $msisdn = $request->get('msisdn');
        if ($msisdn) {
            $responseFromEldorado['data'] = $this->service
                ->getCreditCards($msisdn)
                ->toArray();
            return response()->json($responseFromEldorado);
        }
        return response()->json();
    }

    public function getPlans(OiBRPlansFormRequest $request): JsonResponse
    {
        $plans = $this->service->getPlans($request->pointOfSale, $request->areaCode, $request->paymentType);
        return response()->json($plans);
    }

    public function postRegisterCreditCard(OiBRREgisterCreditCardFormRequest $request): JsonResponse
    {
        $creditCard = $this->service->registerCreditCard($request->pan, $request->year, $request->month);
        return response()->json($creditCard);
    }

    public function eligibility(OiBREligibilityFormRequest $request): JsonResponse
    {
        return $this->service->eligibility($request->msisdn, $request->operation);
    }

    public function oiRedirect(): JsonResponse
    {
        return response()->json($this->service->getResidentialLinks(), Response::HTTP_ACCEPTED);
    }
}
