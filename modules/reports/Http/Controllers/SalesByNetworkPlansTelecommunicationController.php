<?php

namespace Reports\Http\Controllers;

use Reports\Exceptions\FailedReportBuildException;
use Reports\Http\Requests\ReportsFormRequest;
use Reports\Services\SalesByNetworkPlansTelecommunicationService;
use TradeAppOne\Http\Controllers\Controller;

class SalesByNetworkPlansTelecommunicationController extends Controller
{
    private $salesByNetworkPlans;
    public function __construct(SalesByNetworkPlansTelecommunicationService $salesByNetworkPlans)
    {
        $this->salesByNetworkPlans = $salesByNetworkPlans;
    }
    public function getSales(ReportsFormRequest $request)
    {
        try {
            return $this->salesByNetworkPlans->getSalesByNetwork($request->validated());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
