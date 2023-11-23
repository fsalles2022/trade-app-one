<?php

namespace Reports\Http\Controllers;

use Reports\Exceptions\FailedReportBuildException;
use Reports\Http\Requests\ReportsFormRequest;
use Reports\Services\SalesByGroupOfOperationsService;

class TotalSalesByGroupOfOperationsController
{
    private $salesByGroupOfOperationsService;

    public function __construct(SalesByGroupOfOperationsService $salesByGroupOfOperationsService)
    {
        $this->salesByGroupOfOperationsService = $salesByGroupOfOperationsService;
    }

    public function getSales(ReportsFormRequest $request): array
    {
        $period = [
            'since' => data_get($request, 'startDate', '*'),
            'until' => data_get($request, 'endDate', now()->toIso8601String())
        ];
        try {
            return $this->salesByGroupOfOperationsService->getSalesByGroupOfOperations($request->all(), $period);
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
