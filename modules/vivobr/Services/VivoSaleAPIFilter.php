<?php

namespace VivoBR\Services;

class VivoSaleAPIFilter
{
    public $sale;
    public $service;

    public static function filter(array $apiResponse, $sunIds): VivoSaleAPIFilter
    {
        $response       = new VivoSaleAPIFilter();
        $response->sale = collect(data_get($apiResponse, 'vendas', []))->where('id', $sunIds['idVenda'])->first();

        $response->service = collect(data_get($response->sale, 'services', []))
            ->where('id', $sunIds['idServico'])
            ->first();

        return $response;
    }
}
