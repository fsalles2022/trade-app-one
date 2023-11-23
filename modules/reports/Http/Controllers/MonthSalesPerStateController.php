<?php

namespace Reports\Http\Controllers;

use Illuminate\Http\Request;
use Reports\Services\MonthSalesPerStateService;
use Reports\Exceptions\FailedReportBuildException;
use TradeAppOne\Http\Controllers\Controller;

class MonthSalesPerStateController extends Controller
{
    private $monthSalesPerStateService;

    public function __construct(MonthSalesPerStateService $monthSalesPerStateService)
    {
        $this->monthSalesPerStateService = $monthSalesPerStateService;
    }
    
    public function getSales(Request $request): array
    {
        try {
            return $this->monthSalesPerStateService->getMonthSalesPerState($request->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
