<?php

namespace Reports\Http\Controllers;

use Illuminate\Http\Request;
use Reports\Services\TotalSalesWithDeviceService;
use Reports\Exceptions\FailedReportBuildException;
use TradeAppOne\Http\Controllers\Controller;

class TotalSalesWithDeviceController extends Controller
{
    private $totalSalesWithDeviceService;

    public function __construct(TotalSalesWithDeviceService $totalSalesWithDeviceService)
    {
        $this->totalSalesWithDeviceService = $totalSalesWithDeviceService;
    }
    
    public function getSales(Request $request): array
    {
        try {
            return $this->totalSalesWithDeviceService->getTotalSalesWithDevice($request->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
