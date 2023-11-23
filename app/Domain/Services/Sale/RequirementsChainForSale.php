<?php

namespace TradeAppOne\Domain\Services\Sale;

use Discount\Services\BuildTriangulationToSale;

class RequirementsChainForSale
{
    public const CHAIN = [
        BuildTriangulationToSale::class
    ];

    public static function apply(array $service): array
    {
        foreach (self::CHAIN as $link) {
            $requirement = resolve($link);

            if ($requirement instanceof RequirementsForSale) {
                $service = $requirement->apply($service);
            }
        }

        return $service;
    }
}
