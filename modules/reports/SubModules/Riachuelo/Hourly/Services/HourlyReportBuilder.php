<?php

declare(strict_types=1);

namespace Reports\SubModules\Riachuelo\Hourly\Services;

use Reports\SubModules\Core\Models\Hierarchies;
use Reports\SubModules\Core\Models\Operators;
use Reports\SubModules\Core\Models\Sales;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\Hierarchy;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

class HourlyReportBuilder
{
    /** @var Sales */
    protected $sales;

    /** @var Hierarchies */
    protected $hierarchies;

    /** @var Operators */
    protected $operators;

    /** @var PointOfSaleSaleAccumulator[] */
    protected $pointsOfSaleSaleAccumulators = [];

    /** @var HierarchySaleAccumulator[] */
    protected $hierarchiesSaleAccumulators = [];

    public function __construct(
        Sales       $sales,
        Hierarchies $hierarchies,
        Operators   $operators
    ) {
        $this->sales       = $sales;
        $this->hierarchies = $hierarchies;
        $this->operators   = $operators;
    }

    public function build(): void
    {
        $this->mountAccumulators();

        foreach ($this->sales->all() as $sale) {
            $this->processPointOfSaleSaleAccumulators($sale);
            $this->processHierarchyAccumulators($sale);
        }
    }

    private function processPointOfSaleSaleAccumulators(Sale $sale): void
    {
        $pointOfSaleCnpj = data_get($sale, 'pointOfSale.cnpj');

        if (array_key_exists($pointOfSaleCnpj, $this->pointsOfSaleSaleAccumulators) === false) {
            return;
        }

        $this->pointsOfSaleSaleAccumulators[$pointOfSaleCnpj]->accumulate($sale);
    }

    private function processHierarchyAccumulators(Sale $sale): void
    {
        $hierarchy = data_get($sale, 'pointOfSale.hierarchy.slug');

        if (array_key_exists($hierarchy, $this->hierarchiesSaleAccumulators) === false) {
            return;
        }

        $this->hierarchiesSaleAccumulators[$hierarchy]->accumulate($sale);
    }

    private function mountAccumulators(): void
    {
        foreach ($this->hierarchies->all() as $hierarchy) {
            $pointsOfSale = $hierarchy->pointsOfSale;

            foreach ($pointsOfSale as $pointOfSale) {
                $this->mountPointsOfSaleSaleAccumulators($pointOfSale);
            }

            $this->mountHierarchiesSaleAccumulators($hierarchy);
        }
    }

    private function mountPointsOfSaleSaleAccumulators(PointOfSale $pointOfSale): void
    {
        if (array_key_exists($pointOfSale->cnpj, $this->pointsOfSaleSaleAccumulators) === true) {
            return;
        }

        $this->pointsOfSaleSaleAccumulators[$pointOfSale->cnpj] = new PointOfSaleSaleAccumulator($pointOfSale);
    }

    private function mountHierarchiesSaleAccumulators(Hierarchy $hierarchy): void
    {
        $this->hierarchiesSaleAccumulators[$hierarchy->slug] = new HierarchySaleAccumulator($hierarchy);
    }

    public function getOperators(): Operators
    {
        return $this->operators;
    }

    /** @return PointOfSaleSaleAccumulator[] */
    public function getPointsOfSaleSaleAccumulators(): array
    {
        return $this->pointsOfSaleSaleAccumulators;
    }

    /** @return HierarchySaleAccumulator[] */
    public function getHierarchiesSaleAccumulators(): array
    {
        return $this->hierarchiesSaleAccumulators;
    }
}
