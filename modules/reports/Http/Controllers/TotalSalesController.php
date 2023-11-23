<?php

namespace Reports\Http\Controllers;

use Illuminate\Http\Request;
use Reports\Services\TotalSalesService;
use Reports\Exceptions\FailedReportBuildException;
use TradeAppOne\Http\Controllers\Controller;

class TotalSalesController extends Controller
{
    private $totalSalesService;

    public function __construct(TotalSalesService $totalSalesService)
    {
        $this->totalSalesService = $totalSalesService;
    }

    public function getSales(Request $request): array
    {
        try {
            return $this->totalSalesService->getTotalSales($request->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
