<?php

namespace Reports\Http\Controllers;

use Illuminate\Http\Request;
use Reports\Services\TotalSalesTriangulationService;

class TotalSalesTriangulationController
{
    protected $totalSalesTriangulationService;

    public function __construct(TotalSalesTriangulationService $salesTriangulationService)
    {
        $this->totalSalesTriangulationService = $salesTriangulationService;
    }

    public function getSales(Request $request): array
    {
        $period = [
            'since' => data_get($request, 'startDate', '*'),
            'until' => data_get($request, 'endDate', now()->toIso8601String())
        ];

        return $this->totalSalesTriangulationService->getSalesTriangulations($request->all(), $period);
    }
}
