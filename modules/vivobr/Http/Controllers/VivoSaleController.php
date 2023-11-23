<?php

namespace VivoBR\Http\Controllers;

use Illuminate\Http\Request;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Http\Controllers\Controller;
use VivoBR\Services\MapSunSalesService;

class VivoSaleController extends Controller
{
    protected $saleService;
    protected $mapper;

    public function __construct(SaleService $saleService, MapSunSalesService $mapper)
    {
        $this->saleService = $saleService;
        $this->mapper      = $mapper;
    }

    public function update(Request $request)
    {
        $updated = [];
        $sales   = $request->get('vendas', []);
        foreach ($sales as $sale) {
            $service = $this->saleService->findBySunId($sale['id'], $sale['servicos'][0]['id']);

            if ($service instanceof Service) {
                $attributes = $this->mapper->mapAttributesToMongo($sale);
                $service    = $this->saleService->updateService($service, $attributes);
                array_push($updated, $service);
            }
        }

        return $updated ? response()->json($updated) : response()->json([
            'mensagem' => 'Venda não atende aos requisitos'
        ], 403);
    }
}
