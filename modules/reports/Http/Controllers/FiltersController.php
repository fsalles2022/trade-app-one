<?php

namespace Reports\Http\Controllers;

use Reports\Exceptions\FailedReportBuildException;
use Reports\Services\FiltersService;
use TradeAppOne\Http\Controllers\Controller;

class FiltersController extends Controller
{
    private $filtersService;

    public function __construct(FiltersService $filtersService)
    {
        $this->filtersService = $filtersService;
    }

    public function getFilters(): array
    {
        try {
            return $this->filtersService->getFilters(auth()->user());
        } catch (\Exception $exception) {
            //FIXME: Create new Exception
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
