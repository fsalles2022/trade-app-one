<?php

namespace Reports\Http\Controllers;

use Illuminate\Http\Request;
use Reports\Services\MonthSalesPerOperatorService;
use Reports\Exceptions\FailedReportBuildException;
use TradeAppOne\Http\Controllers\Controller;

class MonthSalesPerOperatorController extends Controller
{
    private $monthSalesPerOperatorService;

    public function __construct(MonthSalesPerOperatorService $monthSalesPerOperatorService)
    {
        $this->monthSalesPerOperatorService = $monthSalesPerOperatorService;
    }
    
    public function getSales(Request $request): array
    {
        try {
            return $this->monthSalesPerOperatorService->getMonthSalesPerOperator($request->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
