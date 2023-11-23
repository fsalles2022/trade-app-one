<?php

namespace Reports\Http\Controllers;

use Illuminate\Support\Facades\Log;
use League\Csv\Writer;
use Reports\AnalyticalsReports\Externals\Siv3Export;
use Reports\AnalyticalsReports\MobileApplications\InsuranceEletronicsExport;
use Reports\AnalyticalsReports\MobileApplications\MobileApplicationExport;
use Reports\AnalyticalsReports\MobileApplications\SecuritySystemsExport;
use Reports\Exceptions\FailedReportBuildException;
use Reports\Http\Requests\DefaultAnalyticalCriteriaFormRequest;
use Reports\Services\AnalyticalReportService;
use TradeAppOne\Http\Controllers\Controller;

class AnalyticalReportController extends Controller
{
    /** @var AnalyticalReportService */
    protected $analyticalReportService;
    protected $mobileApplicationExport;
    protected $securitySystemsExport;
    protected $insuranceEletronicsExport;

    /** @var Siv3Export */
    private $siv3Export;

    // TODO needs refactor, remove dependencies
    public function __construct(
        AnalyticalReportService $analyticalReportService,
        MobileApplicationExport $mobileApplicationExport,
        SecuritySystemsExport $securitySystemsExport,
        InsuranceEletronicsExport $insuranceEletronicsExport,
        Siv3Export $siv3Export
    ) {
        $this->analyticalReportService   = $analyticalReportService;
        $this->mobileApplicationExport   = $mobileApplicationExport;
        $this->securitySystemsExport     = $securitySystemsExport;
        $this->insuranceEletronicsExport = $insuranceEletronicsExport;
        $this->siv3Export                = $siv3Export;
    }

    public function analyticalReport(DefaultAnalyticalCriteriaFormRequest $request): ?Writer
    {
        try {
            return $this->analyticalReportService->extractAnalytical($request->except(['page']));
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        } catch (\Throwable $exception2) {
            Log::info('analyticalReport Throwable', ['error' => $exception2->getMessage()]);
        }
    }

    public function analyticalMobileApplications(DefaultAnalyticalCriteriaFormRequest $request): ?Writer
    {
        try {
            return $this->mobileApplicationExport->extractAnalytical($request->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }

    public function analyticalSecuritySystems(DefaultAnalyticalCriteriaFormRequest $request): ?Writer
    {
        try {
            return $this->securitySystemsExport->extractAnalytical($request->except(['page']));
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }

    public function analyticalInsuranceEletronics(DefaultAnalyticalCriteriaFormRequest $request): ?Writer
    {
        try {
            return $this->insuranceEletronicsExport->extractAnalytical($request->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }

    public function claroExternalSale(DefaultAnalyticalCriteriaFormRequest $request): ?Writer
    {
        try {
            return $this->siv3Export->getExternalSales($request->only('startDate', 'endDate'));
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
