<?php

namespace Reports\Http\Controllers;

use Illuminate\Http\Request;
use Reports\Services\TotalSalesPerMonthService;
use Reports\Exceptions\FailedReportBuildException;
use TradeAppOne\Http\Controllers\Controller;

class TotalSalesPerMonthController extends Controller
{
    private $totalSalesPerMonthService;

    public function __construct(TotalSalesPerMonthService $totalSalesPerMonthService)
    {
        $this->totalSalesPerMonthService = $totalSalesPerMonthService;
    }
    
    public function getSales(Request $request): array
    {
        try {
            return $this->totalSalesPerMonthService->getTotalSalesPerMonth($request->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
