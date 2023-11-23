<?php

namespace Reports\Http\Controllers;

use Illuminate\Http\Request;
use Reports\Services\TotalSalesPerStatusService;
use Reports\Exceptions\FailedReportBuildException;
use TradeAppOne\Http\Controllers\Controller;

class TotalSalesPerStatusController extends Controller
{
    private $totalSalesPerStatusService;

    public function __construct(TotalSalesPerStatusService $totalSalesPerStatusService)
    {
        $this->totalSalesPerStatusService = $totalSalesPerStatusService;
    }
    
    public function getSales(Request $request): array
    {
        try {
            return $this->totalSalesPerStatusService->getTotalSalesPerStatus($request->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
