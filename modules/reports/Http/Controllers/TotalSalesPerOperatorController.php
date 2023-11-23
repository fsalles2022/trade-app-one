<?php

namespace Reports\Http\Controllers;

use Illuminate\Http\Request;
use Reports\Services\TotalSalesPerOperatorService;
use Reports\Exceptions\FailedReportBuildException;
use TradeAppOne\Http\Controllers\Controller;

class TotalSalesPerOperatorController extends Controller
{
    private $totalSalesPerOperatorService;

    public function __construct(TotalSalesPerOperatorService $totalSalesPerOperatorService)
    {
        $this->totalSalesPerOperatorService = $totalSalesPerOperatorService;
    }
    
    public function getSales(Request $request): array
    {
        try {
            return $this->totalSalesPerOperatorService->getTotalSalesPerOperator($request->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
