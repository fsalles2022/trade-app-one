<?php

namespace Reports\Http\Controllers;

use Illuminate\Http\Request;
use Reports\Services\LastThirtyDaysSalesPerOperatorService;
use Reports\Exceptions\FailedReportBuildException;
use TradeAppOne\Http\Controllers\Controller;

class LastThirtyDaysSalesPerOperatorController extends Controller
{
    private $lastThirtyDaysSalesPerOperatorService;

    public function __construct(LastThirtyDaysSalesPerOperatorService $lastThirtyDaysSalesPerOperatorService)
    {
        $this->lastThirtyDaysSalesPerOperatorService = $lastThirtyDaysSalesPerOperatorService;
    }
    
    public function getSales(Request $request): array
    {
        try {
            return $this->lastThirtyDaysSalesPerOperatorService->getLastThirtyDaysSalesPerOperator($request->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
