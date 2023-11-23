<?php

namespace FastShop\Services;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Tables\Service;

class ProductNormalize
{

    private $services;

    public function __construct()
    {
        $this->services = Service::all();
    }

    private function findOperationByNameAndOperatorName(string $operationName, string $operatorName)
    {
        return $this->services->first(static function ($service) use ($operationName, $operatorName) {
            return strtolower($service->operation) === strtolower($operationName) &&
                strtolower($service->operator) === strtolower($operatorName);
        });
    }

    public function normalizeAll($rawProducts): Collection
    {
        $normalizedProducts = collect();
        foreach ($rawProducts as $rawProduct) {
            $normalizedProducts->push($this->normalize($rawProduct));
        }
        return $normalizedProducts;
    }
   
    private function findInternet($rawProduct): int
    {
        $operator = data_get($rawProduct, 'operator');
        
        if ($operator === Operations::OI) {
            $description = array_first(data_get($rawProduct, 'details'), null, null);

            if (! $description) {
                return 0;
            }

            preg_match('/([0-9]*)GB de internet/i', $description, $output_array);

            if (! isset($output_array[1])) {
                return 0;
            }

            return (int) $output_array[1];
        } elseif ($operator === Operations::VIVO) {
            $label = data_get($rawProduct, 'label');

            if (! $label) {
                return 0;
            }

            preg_match('/([0-9\,]*)GB/i', $label, $output_array);

            if (! isset($output_array[1])) {
                return 0;
            }

            return (int) str_replace(',', '.', $output_array[1]);
        } elseif ($operator === Operations::CLARO) {
            $label = data_get($rawProduct, 'label');

            if (! $label) {
                return 0;
            }

            preg_match('/([0-9\,]*)GB/i', $label, $output_array);

            if (! isset($output_array[1])) {
                return 0;
            }

            return (int) str_replace(',', '.', $output_array[1]);
        } elseif ($operator === Operations::NEXTEL) {
            $label = data_get($rawProduct, 'label');

            if (! $label) {
                return 0;
            }

            preg_match('/([0-9\,]*)GB/i', $label, $output_array);

            if (! isset($output_array[1])) {
                return 0;
            }

            return (int) str_replace(',', '.', $output_array[1]);
        }

        return 0;
    }

    public function normalize($rawProduct): Collection
    {
        $operation = data_get($rawProduct, 'operation');
        $operator  = data_get($rawProduct, 'operator');
        $service   = $this->findOperationByNameAndOperatorName($operation, $operator);

        return collect([
            'code' => data_get($rawProduct, 'product'),
            'title' => data_get($rawProduct, 'label'),
            'areaCode' => data_get($rawProduct, 'areaCode', 11),
            'loyaltyMonths' => data_get($rawProduct, 'promotion.loyalty', 0),
            'serviceId' => $service->id,
            'internet' => $this->findInternet($rawProduct),
            'minutes' => 0,
            'price' => data_get($rawProduct, 'price'),
            'original' => json_encode($rawProduct)
        ]);
    }
}
