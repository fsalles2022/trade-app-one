<?php

namespace NextelBR\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use NextelBR\Http\Requests\EligibilityFormRequest;
use NextelBR\Http\Requests\NextelBRLogM4uFormRequest;
use NextelBR\Http\Requests\ValidationBankDataFormRequest;
use NextelBR\Services\NextelBRService;
use TradeAppOne\Events\PreAnalysisEvent;
use TradeAppOne\Features\Customer\Adapter\CustomerNested;

class NextelBRController
{
    protected $service;

    public function __construct(NextelBRService $service)
    {
        $this->service = $service;
    }

    public function getDomains(): JsonResponse
    {
        return response()->json($this->service->domains());
    }

    public function postEligibility(EligibilityFormRequest $request)
    {
        $user      = Auth::user();
        $eligibles = $this->service->eligibility($request->all(), $user);
        event(new PreAnalysisEvent(new CustomerNested($request->all())));
        if ($eligibles->isEmpty()) {
            return \response()->json(
                ['message' => trans('nextelBR::messages.eligibility.emptyPlans')],
                Response::HTTP_NOT_ACCEPTABLE
            );
        }
        return $eligibles;
    }


    public function postLogM4u(NextelBRLogM4uFormRequest $request)
    {
        $result = $this->service->logM4uSuccess($request->all());
        if ($result) {
            return $result;
        }
        return response()->json([], Response::HTTP_NOT_ACCEPTABLE);
    }

    public function validationBankData(ValidationBankDataFormRequest $request)
    {
        $process =  $this->service->validateBankData($request->validated());

        if ($process->isSuccess()) {
            return response()->json(['message' => trans('nextelBR::messages.bank_data.success') ], Response::HTTP_OK);
        }

        $message = data_get($process->toArray(), 'mensagem');
        return response()->json(['message' => $message], Response::HTTP_PRECONDITION_FAILED);
    }
}
