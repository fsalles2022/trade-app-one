<?php

declare(strict_types=1);

namespace Reports\AnalyticalsReports\Input;

class SalesCollectionInlineInput implements SalesCollectionMappableInterface
{
    /** @var SaleMappableInterface[] */
    protected $sales;

    /** @param SaleMappableInterface[] $sales */
    public function __construct(array $sales)
    {
        $this->sales = $sales;
    }

    /** @return array[] */
    public function toArray(): array
    {
        $salesInlinesMapped = [];

        foreach ($this->sales as $sale) {
            foreach ($sale->toArray() as $saleLine) {
                $salesInlinesMapped[] = $saleLine;
            }
        }

        return $salesInlinesMapped;
    }
}
