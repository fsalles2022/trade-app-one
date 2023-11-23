<?php

namespace FastShop\Adapters;

use Buyback\Services\Waybill;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\MoneyHelper;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

class SimulationAdapter
{
    public static function adapter(Collection $products, array $device): Collection
    {
        $results = [];

        foreach ($products as $product) {
            $results[] = [
                'aparelho' => [
                    [
                        'open' => false,
                        'parcelas' => null,
                        'preco' => MoneyHelper::formatMoney(data_get($device, 'labelPrice')),
                        'preco_a_vista' => MoneyHelper::formatMoney(data_get($device, 'price')),
                        'sku' => trim(data_get($device, 'codeProduct'))
                    ]
                ],
                'compra_url' => [url()->full()],
                'plano' => [
                    [
                        'id' => $product->id,
                        'preco' => MoneyHelper::formatMoney($product->price),
                        'nome' => $product->title,
                        'ddd' => $product->areaCode,
                        'finalizacao' => $product->loyaltyMonths,
                        'internet' => ($product->internet * 1000),
                        'minutos' => $product->minutes,
                        'operadora' => $product->service->operator,
                        'mensagens' => [
                            'internet' => null,
                            'minutos' => null,
                            'sms' => null,
                            'sva' => null
                        ]
                    ]
                ]
            ];
        }

        return collect($results);
    }
}
