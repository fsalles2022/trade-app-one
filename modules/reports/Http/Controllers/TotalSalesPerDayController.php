<?php

namespace Reports\Http\Controllers;

use Illuminate\Http\Request;
use Reports\Services\TotalSalesPerDayService;
use Reports\Exceptions\FailedReportBuildException;
use TradeAppOne\Http\Controllers\Controller;

class TotalSalesPerDayController extends Controller
{
    private $totalSalesPerDayService;

    public function __construct(TotalSalesPerDayService $totalSalesPerDayService)
    {
        $this->totalSalesPerDayService = $totalSalesPerDayService;
    }
    
    public function getSales(Request $request): array
    {
        try {
            return $this->totalSalesPerDayService->getTotalSalesPerDay($request->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
