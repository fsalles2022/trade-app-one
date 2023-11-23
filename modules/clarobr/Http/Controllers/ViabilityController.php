<?php

declare(strict_types=1);

namespace ClaroBR\Http\Controllers;

use ClaroBR\Services\ViabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use TradeAppOne\Http\Controllers\Controller;

class ViabilityController extends Controller
{
    /** @var ViabilityService */
    public $viabilityService;

    public function __construct(ViabilityService $viabilityService)
    {
        $this->viabilityService = $viabilityService;
    }

    public function show(string $serviceTransaction): JsonResponse
    {
        return response()->json(
            $this->viabilityService->getViability($serviceTransaction),
            Response::HTTP_OK
        );
    }
}
