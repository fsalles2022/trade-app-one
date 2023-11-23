<?php

namespace TradeAppOne\Domain\Repositories\QueryObject;

use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;

class FilteredQueryObject implements QueryObject
{
    private $filters;
    private $queryBuilder;

    public function __construct(ElasticsearchQueryBuilder $queryBuilder, array $filters)
    {
        $this->queryBuilder = $queryBuilder;
        $this->filters      = $filters;
    }

    public function getQuery()
    {
        return $this->processParameters();
    }

    private function processParameters(): ElasticsearchQueryBuilder
    {
        $convertionTable = $this->getConvertedTable();

        foreach ($this->filters as $key => $values) {
            if ($this->validKey($key) && $this->validValues($values)) {
                $this->queryBuilder->whereIn($convertionTable[$key], $values);
            }
        }

        return $this->queryBuilder->get();
    }

    private function validKey($key): bool
    {
        return array_key_exists($key, $this->getConvertedTable());
    }

    private function validValues($values): bool
    {
        return (is_array($values) && $this->allValuesPrimitive($values));
    }

    private function allValuesPrimitive($values):bool
    {
        foreach ($values as $value) {
            if (! is_scalar($value)) {
                return false;
            }
        }

        return true;
    }

    private function getConvertedTable(): array
    {
        return [
            'pointsOfSale' => 'pointofsale_cnpj',
            'operators' => 'service_operators',
        ];
    }
}
