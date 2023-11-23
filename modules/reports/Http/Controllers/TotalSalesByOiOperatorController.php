<?php

namespace Reports\Http\Controllers;

use Illuminate\Http\Request;
use Reports\Exceptions\FailedReportBuildException;
use Reports\Services\FilterSalesByOperatorService;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Http\Controllers\Controller;

class TotalSalesByOiOperatorController extends Controller
{
    private $filterSalesByOperatorService;

    public function __construct(FilterSalesByOperatorService $filterSalesByOperatorService)
    {
        $this->filterSalesByOperatorService = $filterSalesByOperatorService;
    }

    public function getSales(Request $request): array
    {
        try {
            return $this->filterSalesByOperatorService->getFilterSalesByOperator(Operations::OI, request()->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
