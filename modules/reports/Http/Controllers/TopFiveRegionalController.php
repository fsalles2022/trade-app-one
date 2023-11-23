<?php

namespace Reports\Http\Controllers;

use Reports\Exceptions\FailedReportBuildException;
use Reports\Services\TopFiveRegionalService;
use TradeAppOne\Http\Controllers\Controller;

class TopFiveRegionalController extends Controller
{
    protected $topFiveRegionalService;

    public function __construct(TopFiveRegionalService $topFiveRegionalService)
    {
        $this->topFiveRegionalService = $topFiveRegionalService;
    }

    public function get()
    {
        try {
            return $this->topFiveRegionalService->get(request()->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
