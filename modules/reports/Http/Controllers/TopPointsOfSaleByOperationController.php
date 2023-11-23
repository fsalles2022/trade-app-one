<?php

namespace Reports\Http\Controllers;

use Reports\Exceptions\FailedReportBuildException;
use Reports\Http\Requests\ReportsFormRequest;
use Reports\Services\TopPointsOfSaleByOperationService;
use TradeAppOne\Http\Controllers\Controller;

class TopPointsOfSaleByOperationController extends Controller
{
    protected $topPointsOfSaleByOperationService;

    public function __construct(TopPointsOfSaleByOperationService $topPointsOfSaleByOperationService)
    {
        $this->topPointsOfSaleByOperationService = $topPointsOfSaleByOperationService;
    }

    public function getSales(ReportsFormRequest $request)
    {
        try {
            return $this->topPointsOfSaleByOperationService->getSales($request->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
