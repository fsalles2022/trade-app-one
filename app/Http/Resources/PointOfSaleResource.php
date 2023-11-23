<?php

namespace TradeAppOne\Http\Resources;

use TradeAppOne\Domain\Models\Tables\PointOfSale;

class PointOfSaleResource
{
    public function map(PointOfSale $pointOfSale)
    {
        return [
            "id"                     => data_get($pointOfSale, 'id'),
            "slug"                   => data_get($pointOfSale, 'slug'),
            "label"                  => data_get($pointOfSale, 'label'),
            "cnpj"                   => data_get($pointOfSale, 'cnpj'),
            "tradingName"            => data_get($pointOfSale, 'tradingName'),
            "companyName"            => data_get($pointOfSale, 'companyName'),
            "providerIdentifiers"    => data_get($pointOfSale, 'providerIdentifiers'),
            "state"                  => data_get($pointOfSale, 'state'),
            "city"                   => data_get($pointOfSale, 'city'),
            "zipCode"                => data_get($pointOfSale, 'zipCode'),
            "areaCode"               => data_get($pointOfSale, 'areaCode'),
            "hierarchy"              => [
                "id" => data_get($pointOfSale, 'hierarchy.id'),
                "slug" => data_get($pointOfSale, 'hierarchy.slug'),
                "label" => data_get($pointOfSale, 'hierarchy.label'),
                "sequence" => data_get($pointOfSale, 'hierarchy.sequence')
            ],
            "network"                => [
                "id"                => data_get($pointOfSale, 'network.id'),
                "slug"              => data_get($pointOfSale, 'network.slug'),
                "label"             => data_get($pointOfSale, 'network.label'),
                "cnpj"              => data_get($pointOfSale, 'network.cnpj'),
                "companyName"       => data_get($pointOfSale, 'network.companyName'),
                "availableServices" => data_get($pointOfSale, 'network.availableServices'),
            ]
        ];
    }
}
