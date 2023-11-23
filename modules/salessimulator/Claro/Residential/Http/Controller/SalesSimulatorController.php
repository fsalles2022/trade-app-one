<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Http\Controller;

use Illuminate\Http\JsonResponse;
use SalesSimulator\Claro\Residential\Http\Requests\SalesSimulatorRequest;
use SalesSimulator\Claro\Residential\Services\SalesSimulatorService;
use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Http\Controllers\Controller;

class SalesSimulatorController extends Controller
{
    /** @var SalesSimulatorService */
    private $salesSimulatorService;

    public function __construct(SalesSimulatorService $salesSimulatorService)
    {
        $this->salesSimulatorService = $salesSimulatorService;
    }

    public function getPlansAndPromotions(SalesSimulatorRequest $request): JsonResponse
    {
        return response()->json(
            $this->salesSimulatorService->getPlansAndPromotions($request->validated()),
            Response::HTTP_OK
        );
    }
}
