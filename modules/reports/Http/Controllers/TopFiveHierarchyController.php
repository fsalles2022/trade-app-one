<?php

namespace Reports\Http\Controllers;

use Reports\Exceptions\FailedReportBuildException;
use Reports\Services\TopFiveHierarchyService;
use TradeAppOne\Http\Controllers\Controller;

class TopFiveHierarchyController extends Controller
{
    protected $topFiveHierarchyService;

    public function __construct(TopFiveHierarchyService $topFiveHierarchyService)
    {
        $this->topFiveHierarchyService = $topFiveHierarchyService;
    }

    public function get()
    {
        try {
            return $this->topFiveHierarchyService->get(request()->all());
        } catch (\Exception $exception) {
            throw new FailedReportBuildException($exception->getMessage());
        }
    }
}
