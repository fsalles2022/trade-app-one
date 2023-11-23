<?php

namespace Reports\Http\Controllers;

use Illuminate\Http\Response;
use Reports\Http\Requests\SaleReportFormRequest;
use Reports\Services\SalesReportService;
use TradeAppOne\Http\Controllers\Controller;

class SalesReportController extends Controller
{
    private $saleReportService;

    public function __construct(SalesReportService $saleReportService)
    {
        $this->saleReportService = $saleReportService;
    }

    public function index(SaleReportFormRequest $request)
    {
        $page    = $request->get('page', false);
        $filters = $request->validated();

        $sales = $page
            ? $this->saleReportService->filterAndPaginate($filters, $page)
            : $this->saleReportService->filter($filters);

        return response()->json($sales, Response::HTTP_OK);
    }
}
