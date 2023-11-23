<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Support\Collection;

interface SaleMapeable
{
    public function mapToTable(string $source, Collection $salesFromSun): Collection;

    public function mapRow($source, array $saleFromSun);
}
