<?php

namespace McAfee\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use McAfee\Http\Requests\McAfeeFormRequest;
use McAfee\Services\McAfeeInternetService;
use McAfee\Services\McAfeeService;
use TradeAppOne\Http\Controllers\Controller;

class McAfeeController extends Controller
{
    protected $mcAfeeService;
    protected $mcAfeeInternetService;

    public function __construct(McAfeeService $mcAfeeService, McAfeeInternetService $mcAfeeInternetService)
    {
        $this->mcAfeeService         = $mcAfeeService;
        $this->mcAfeeInternetService = $mcAfeeInternetService;
    }

    public function plans(McAfeeFormRequest $request): JsonResponse
    {
        $operation = $request->get('operation');
        $user      = $request->user();

        return response()->json(
            $this->mcAfeeService->plans($user, $operation),
            Response::HTTP_OK
        );
    }

    public function onByInternet(McAfeeFormRequest $request): JsonResponse
    {
        $data         = $request->validated();
        $subscription = $this->mcAfeeInternetService->subscription($data);
        return response()->json($subscription, Response::HTTP_OK);
    }

    public function updateStatusPayment(McAfeeFormRequest $request, string $serviceTransaction): void
    {
        $this->mcAfeeService->updateStatusPayment($request->validated(), $serviceTransaction);
    }
}
