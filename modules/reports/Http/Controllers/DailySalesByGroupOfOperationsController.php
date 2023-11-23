<?php

namespace Reports\Http\Controllers;

use Carbon\Carbon;
use Reports\Exceptions\FailedReportBuildException;
use Reports\Http\Requests\ReportsFormRequest;
use Reports\Services\SalesByGroupOfOperationsService;

class DailySalesByGroupOfOperationsController
{
    private $salesByGroupOfOperationsService;

    public function __construct(SalesByGroupOfOperationsService $salesByGroupOfOperationsService)
    {
        $this->salesByGroupOfOperationsService = $salesByGroupOfOperationsService;
    }

    public function getSales(ReportsFormRequest $request): array
    {
        $period = [
            'since' => (Carbon::now())->startOfDay()->toIso8601String(),
            'until' => (Carbon::now())->toIso8601String()
        ];
        try {
            return $this->salesByGroupOfOperationsService->getSalesByGroupOfOperations($request->all(), $period);
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
