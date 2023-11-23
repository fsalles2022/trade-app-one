<?php

declare(strict_types=1);

namespace SurfPernambucanas\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SurfPernambucanas\Http\Requests\SurfPernambucanasFormRequest;
use SurfPernambucanas\Services\PagtelService;
use TradeAppOne\Http\Controllers\Controller;

class SurfPernambucanasController extends Controller
{
    public function subscriberActivate(SurfPernambucanasFormRequest $request): JsonResponse
    {
        $adapter = $this->getService($request->get('idClient'))->subscriberActivate(
            $request->get('iccid'),
            $request->get('areaCode'),
            $request->get('cpf')
        );

        return $adapter->adapt();
    }

    public function allocateMsisdn(SurfPernambucanasFormRequest $request): JsonResponse
    {
        $adapter = $this->getService($request->get('idClient'))->allocateMsisdn($request->get('iccid'));

        return $adapter->adapt();
    }

    public function plans(SurfPernambucanasFormRequest $request): JsonResponse
    {
        $adapter = $this->getService($request->get('idClient'))->plans($request->get('msisdn'));

        return $adapter->adapt();
    }

    public function activationPlans(Request $request): JsonResponse
    {
        $service = $this->getService($request->get('idClient'));

        $adapter  = $service->activationPlans();
        $planType = $request->get('planType');
        $plans    = $adapter->getAdapted()['plans'];

        $result = $service->validatePlanType($planType, $plans);

        return response()->json([
            'plans' => $result
        ]);
    }

    public function utils(Request $request): JsonResponse
    {
        return \response()->json($this->getService($request->get('idClient'))->utils(), Response::HTTP_OK);
    }

    public function domains(Request $request): JsonResponse
    {
        return \response()->json($this->getService($request->get('idClient'))->domains(), Response::HTTP_OK);
    }

    public function nextPortinDate(Request $request): JsonResponse
    {
        return \response()->json($this->getService($request->get('idClient'))->calculatePortinDate(\now()), Response::HTTP_OK);
    }

    private function getService(?string $clientId = null): PagtelService
    {
        return app()->make(PagtelService::class, [
            'client' => $clientId,
        ]);
    }
}
