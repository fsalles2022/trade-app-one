<?php

namespace Reports\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Reports\Exceptions\FailedReportBuildException;
use Reports\Services\SalesByGroupOfOperationsService;

class MonthSalesByGroupOfOperationsController extends Controller
{
    private $salesByGroupOfOperationsService;

    public function __construct(SalesByGroupOfOperationsService $salesByGroupOfOperationsService)
    {
        $this->salesByGroupOfOperationsService = $salesByGroupOfOperationsService;
    }

    public function getSales(Request $request): array
    {
        try {
            return $this->salesByGroupOfOperationsService->getSalesByGroupOfOperations($request->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
