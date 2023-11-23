<?php

namespace Reports\Http\Controllers;

use Reports\Exceptions\FailedReportBuildException;
use Reports\Http\Requests\ReportsFormRequest;
use Reports\Services\TopPointOfSalesByOperatorService;
use TradeAppOne\Http\Controllers\Controller;

class TopPointOfSalesByOperatorController extends Controller
{
    private $topsPointsOfSalesByOperatorService;

    public function __construct(TopPointOfSalesByOperatorService $topsPointsOfSalesByOperatorService)
    {
        $this->topsPointsOfSalesByOperatorService = $topsPointsOfSalesByOperatorService;
    }

    public function getTop(ReportsFormRequest $request): array
    {
        try {
            return $this->topsPointsOfSalesByOperatorService->getTopsPointsOfSalesByOperator($request->validated());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
