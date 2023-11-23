<?php

namespace Reports\Adapters\QueryResults;

use Illuminate\Support\Collection;
use Reports\Enum\OperationsGroupColors;
use TradeAppOne\Domain\Components\Helpers\MoneyHelper;
use TradeAppOne\Domain\Enumerators\GroupOfOperations;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;

class TopPointsOfSaleByOperationAdapter
{

    protected $pointOfSaleRepository;

    public function __construct(PointOfSaleRepository $pointOfSaleRepository)
    {
        $this->pointOfSaleRepository = $pointOfSaleRepository;
    }

    public function adapt(Collection $queryResult)
    {
        $buckets = data_get($queryResult, 'aggregations.POINTS_OF_SALES.buckets');

        $pointsOfSale = [];
        $CONTROLE     = [];
        $POS_PAGO     = [];
        $PRE_PAGO     = [];

        foreach ($buckets as $bucket) {
            array_push($pointsOfSale, data_get($bucket, 'key'));

            $prePagoValue  = data_get($bucket, 'PRE_PAGO.REVENUES.value', 0);
            $posPagoValue  = data_get($bucket, 'POS_PAGO.REVENUES.value', 0);
            $controleValue = data_get($bucket, 'CONTROLE.REVENUES.value', 0);

            array_push($PRE_PAGO, [
                'y'        => data_get($bucket, 'PRE_PAGO.doc_count', 0),
                'revenues' => MoneyHelper::formatMoney($prePagoValue)
            ]);

            array_push($POS_PAGO, [
                'y' => data_get($bucket, 'POS_PAGO.doc_count', 0),
                'revenues' => MoneyHelper::formatMoney($posPagoValue)
            ]);

            array_push($CONTROLE, [
                'y' => data_get($bucket, 'CONTROLE.doc_count', 0),
                'revenues' => MoneyHelper::formatMoney($controleValue)
            ]);
        }

        $data = [
            [
                'color' => OperationsGroupColors::PRE_PAGO,
                'name' => trans('constants.group_of_operations.' . GroupOfOperations::PRE_PAGO),
                'data' => $PRE_PAGO
            ],
            [
                'color' => OperationsGroupColors::POS_PAGO,
                'name' => trans('constants.group_of_operations.' . GroupOfOperations::POS_PAGO),
                'data' => $POS_PAGO
            ],
            [
                'color' => OperationsGroupColors::CONTROLE,
                'name' => trans('constants.group_of_operations.' . GroupOfOperations::CONTROLE),
                'data' => $CONTROLE
            ]
        ];

        return [
            'pointsOfSales' => $this->getLabelPointOfSale($pointsOfSale),
            'sales' => array_values($data)
        ];
    }

    private function getLabelPointOfSale(array $cnpjs)
    {
        $pointsOfSale = $this->pointOfSaleRepository->whereIn('cnpj', $cnpjs);
        $labels       = [];

        foreach ($cnpjs as $cnpj) {
            $pointOfSale = $pointsOfSale->where('cnpj', $cnpj)->first();
            $label       = $pointOfSale->label ?? $cnpj;

            array_push($labels, $label);
        }

        return $labels;
    }
}
