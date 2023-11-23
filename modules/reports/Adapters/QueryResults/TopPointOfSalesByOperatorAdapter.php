<?php

namespace Reports\Adapters\QueryResults;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Reports\Enum\ColorsOperators;
use Reports\Helpers\ColorHelper;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepository;

class TopPointOfSalesByOperatorAdapter
{
    public static function adapt(Collection $collection)
    {
        $data               = [];
        $pointsOfSale       = [];
        $listOfPointsOfSale = data_get($collection, 'aggregations.point_of_sales.buckets', []);
        foreach ($listOfPointsOfSale as $pointOfSale) {
            $cnpj                  = data_get($pointOfSale, 'key', '');
            $pointOfSaleRepository = resolve(PointOfSaleRepository::class);
            $pointOfSaleInstance   = $pointOfSaleRepository->findOneByCnpj($cnpj);
            array_push($pointsOfSale, $pointOfSaleInstance->label);
            $operators        = collect(data_get($pointOfSale, 'operators.buckets'));
            $availableService = data_get(Auth::user(), 'role.network.availableServices.' .
                Operations::LINE_ACTIVATION, []);
            foreach ($availableService as $operator => $operations) {
                $values = $operators->where('key', $operator)->first();
                if ($values) {
                    $quantity = data_get($values, 'doc_count');
                } else {
                    $quantity = 0;
                }
                if (array_key_exists($operator, $data)) {
                    array_push($data[$operator]['data'], $quantity);
                } else {
                    $data[$operator] = [
                        'name'  => ucwords(strtolower($operator)),
                        'data'  => [$quantity],
                        'stack' => $operator,
                        'color' => ColorHelper::colorToRGB(ConstantHelper::getValue(ColorsOperators::class, $operator))
                    ];
                }
            }
        }
        return [
            'data'         => array_values($data),
            'pointsOfSale' => $pointsOfSale
        ];
    }
}
