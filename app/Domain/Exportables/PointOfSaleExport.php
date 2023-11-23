<?php

namespace TradeAppOne\Domain\Exportables;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;

class PointOfSaleExport
{
    protected $pointsOfSale;

    public function __construct(Collection $pointsOfSale)
    {
        $this->pointsOfSale = $pointsOfSale;
    }

    public function writeCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $header   = [];
        $header[] = $this->header();
        $lines    = array_merge($header, $this->adapter($this->pointsOfSale));

        return CsvHelper::exportDataToCsvFile($lines, 'pos-export');
    }

    private function header(): array
    {
        return [
            'rede',
            'slug',
            'label',
            'razaoSocial',
            'nomeFantasia',
            'ddd',
            'cnpj',
            'estado',
            'cidade',
            'cep',
            'bairro',
            'logradouro',
            'numero',
            'claro',
            'tim',
            'oi',
            'nextelCod',
            'nextelRef',
            'regional'
        ];
    }

    private function adapter($pointOfSales): array
    {
        $adapter = [];

        foreach ($pointOfSales as $pointOfSale) {
            $adapter[] = [
               $pointOfSale->network->slug,
               $pointOfSale->slug,
               $pointOfSale->label,
               $pointOfSale->tradingName,
               $pointOfSale->companyName,
               $pointOfSale->areaCode,
               $pointOfSale->cnpj,
               $pointOfSale->state,
               $pointOfSale->city,
               $pointOfSale->zipCode,
               $pointOfSale->neighborhood,
               $pointOfSale->local,
               $pointOfSale->number,
               data_get($pointOfSale->providerIdentifiers, 'CLARO'),
               data_get($pointOfSale->providerIdentifiers, 'TIM'),
               data_get($pointOfSale->providerIdentifiers, 'OI'),
               data_get($pointOfSale->providerIdentifiers, 'NEXTEL.cod'),
               data_get($pointOfSale->providerIdentifiers, 'NEXTEL.ref'),
               data_get($pointOfSale->hierarchy, 'slug', '-')
            ];
        }

        return $adapter;
    }
}
