<?php

namespace TradeAppOne\Domain\Services\Sale;

interface RequirementsForSale
{
    public function apply(array $service): array;
}
