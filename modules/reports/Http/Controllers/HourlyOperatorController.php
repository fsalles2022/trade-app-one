<?php

namespace Reports\Http\Controllers;

use Illuminate\Http\Request;
use Reports\Exceptions\FailedReportBuildException;
use Reports\Services\HourlyTotalSalesService;
use TradeAppOne\Http\Controllers\Controller;

class HourlyOperatorController extends Controller
{
    private $lastThirtyDaysSalesPerOperatorService;

    public function __construct(HourlyTotalSalesService $lastThirtyDaysSalesPerOperatorService)
    {
        $this->lastThirtyDaysSalesPerOperatorService = $lastThirtyDaysSalesPerOperatorService;
    }

    public function getSales(Request $request)
    {
        try {
            return $this->lastThirtyDaysSalesPerOperatorService->getHourly($request->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
