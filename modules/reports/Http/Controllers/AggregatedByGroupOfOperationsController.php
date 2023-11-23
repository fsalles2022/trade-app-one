<?php

namespace Reports\Http\Controllers;

use Reports\Enum\PreControlePosLineActivationOperations;
use Reports\Exceptions\ReportExceptions;
use Reports\Http\Requests\ReportsFormRequest;
use Reports\Services\SalesByAggregatedService;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Http\Controllers\Controller;

class AggregatedByGroupOfOperationsController extends Controller
{
    private $salesByAggregatedService;

    public function __construct(SalesByAggregatedService $salesByAggregatedService)
    {
        $this->salesByAggregatedService = $salesByAggregatedService;
    }

    public function getSales(ReportsFormRequest $request)
    {
        try {
            $groups = ConstantHelper::getAllConstants(PreControlePosLineActivationOperations::class);

            return $this->salesByAggregatedService->getResume($request->validated(), $groups);
        } catch (\Exception $exception) {
            throw ReportExceptions::failedReportBuild($exception->getMessage());
        }
    }
}
