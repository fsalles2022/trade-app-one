<?php

namespace Reports\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Reports\Exceptions\FailedReportBuildException;
use Reports\Http\Requests\RefusedSaleReportFormRequest;
use Reports\Services\RefusedSaleReportService;
use TradeAppOne\Domain\Enumerators\Permissions\RefusedSaleReportPermission;
use TradeAppOne\Http\Controllers\Controller;

class RefusedSaleReportController extends Controller
{
    protected $refusedSaleService;

    public function __construct(RefusedSaleReportService $negatedSaleReportService)
    {
        $this->refusedSaleService = $negatedSaleReportService;
    }

    public function getRefused(RefusedSaleReportFormRequest $request)
    {
        try {
            hasPermissionOrAbort(RefusedSaleReportPermission::getFullName(RefusedSaleReportPermission::VIEW));
            $refuseds = $this->refusedSaleService->filter($request->validated());
            return response()->json($refuseds, Response::HTTP_OK);
        } catch (Exception $e) {
            throw new FailedReportBuildException($e->getMessage());
        }
    }

    public function exportCsv(RefusedSaleReportFormRequest $request)
    {
        try {
            hasPermissionOrAbort(RefusedSaleReportPermission::getFullName(RefusedSaleReportPermission::EXPORT));
            return $this->refusedSaleService->getRefusedSalesExport($request->validated());
        } catch (Exception $e) {
            throw new FailedReportBuildException($e->getMessage());
        }
    }
}
