<?php

namespace ClaroBR\Services\UpdateAttributes;

use ClaroBR\Connection\SivConnection;
use ClaroBR\Services\ClaroBRFillDependents;
use ClaroBR\Services\UpdateAttributes\Resources\DumpPromotions;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Exceptions\BusinessExceptions\ProductNotFoundException;

class ClaroBRUpdateDependentsService implements ClaroBRUpdateAttributes
{
    protected $saleService;
    protected $connection;

    public function __construct(SaleService $saleService, SivConnection $connection)
    {
        $this->saleService = $saleService;
        $this->connection  = $connection;
    }

    public function update(array $options = []): Collection
    {
        $networks = data_get($options, 'network');
        if ($networks && is_array($networks)) {
            foreach ($networks as $network) {
                $sales = $this->saleService->getByNetworkSlug($network);
                return $this->filterSalesWithDependents($sales);
            }
        } elseif ($serviceTransaction = data_get($options, 'serviceTransaction')) {
            $pickedService = $this->saleService->findService($serviceTransaction);
            return collect([$this->updatedDependentInformationBasedOnSiv($pickedService)]);
        }
    }

    private function filterSalesWithDependents(Collection $sales): Collection
    {
        $collectionOfSalesUpdated = new Collection();
        foreach ($sales as $sale) {
            $servicesWithDependent = $sale->services
                ->where('operator', Operations::CLARO)
                ->where('dependents', '!=', null);
            foreach ($servicesWithDependent as $serviceWithDependent) {
                $serviceUpdated = $this->updatedDependentInformationBasedOnSiv($serviceWithDependent);
                $collectionOfSalesUpdated->push($serviceUpdated);
            }
        }
        return $collectionOfSalesUpdated;
    }

    private function updatedDependentInformationBasedOnSiv(Service $serviceWithDependent)
    {
        if (! data_get($serviceWithDependent, 'dependents.0.promotion.product')) {
            try {
                $mappedDependents = (new ClaroBRFillDependents($this->connection))
                    ->fill(
                        config('integrations.siv.sentinel'),
                        $serviceWithDependent->dependents,
                        $serviceWithDependent->areaCode
                    );
            } catch (ProductNotFoundException $exception) {
                $mappedDependents = $serviceWithDependent->dependents;
            }
            $mappedDependents = $this->mapPromotionFromDump($mappedDependents);
            return $this->saleService->updateService($serviceWithDependent, ['dependents' => $mappedDependents]);
        }
        return $serviceWithDependent;
    }

    private function mapPromotionFromDump(array $preMappedDependents)
    {
        $dependentsWithPromotionFilled = [];
        foreach ($preMappedDependents as $dependent) {
            if ($promotion = data_get($dependent, 'promotion')) {
                if (is_array($promotion)) {
                    $promotionFromDump = DumpPromotions::promotions()
                        ->where('product', data_get($promotion, 'product'))
                        ->first();
                } else {
                    $promotionFromDump = DumpPromotions::promotions()
                        ->where('product', $promotion)
                        ->first();
                }
                $dependent['promotion'] = $promotionFromDump;
                if ($price = data_get($dependent, 'price')) {
                    $dependent['price'] = $price + data_get($dependent, 'promotion.price', 0);
                }
            }
            array_push($dependentsWithPromotionFilled, $dependent);
        }
        return $dependentsWithPromotionFilled;
    }
}
