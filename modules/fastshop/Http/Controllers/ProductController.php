<?php

namespace FastShop\Http\Controllers;

use FastShop\Enumerators\SimulatorFilterOptions;
use FastShop\Http\Requests\SimulationFormRequest;
use FastShop\Services\FastShopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{

    private $fastShopService;

    public function __construct(FastShopService $fastShopService)
    {
        $this->fastShopService = $fastShopService;
    }

    public function productSimulation(SimulationFormRequest $request): JsonResponse
    {
        $simulation = $this->fastShopService->simulate($request->validated(), $request->query->all());
        return response()->json([
            'filtros' => SimulatorFilterOptions::FILTERS,
            'resultado' => $simulation
        ]);
    }

    public function products(): JsonResponse
    {
        return response()->json($this->fastShopService->getProducts());
    }
}
