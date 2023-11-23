<?php

namespace Reports\Services;

use Reports\Adapters\QueryResults\SalesToThirdPartiesAdapter;
use Reports\Criteria\DefaultCriteria;
use Reports\Enum\PreControlePosLineActivationOperations;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;
use TradeAppOne\Domain\Enumerators\GroupOfOperations;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class SalesToThirdPartiesService
{
    private $saleReportRepository;

    public function __construct(SaleReportRepository $repository)
    {
        $this->saleReportRepository = $repository;
    }

    public function getSales(array $filters = []): array
    {
        $query = $this->getQuery();

        $filteredQuery = (new DefaultCriteria($filters))->apply($query);

        $collection = $this->saleReportRepository->getFilteredByContext($filteredQuery);

        return SalesToThirdPartiesAdapter::adapt($collection);
    }

    private function getQuery(): ElasticQueryBuilder
    {
        $aggsPlanPre = (new ElasticsearchAggregationStructure(GroupOfOperations::PRE_PAGO))
            ->filterTermByFieldValue('service_operation.keyword', PreControlePosLineActivationOperations::PRE);

        $aggsPlanPos = (new ElasticsearchAggregationStructure(GroupOfOperations::POS_PAGO))
            ->filterTermByFieldValue('service_operation.keyword', PreControlePosLineActivationOperations::POS)
            ->brother($aggsPlanPre);

        $aggsControle = (new ElasticsearchAggregationStructure(GroupOfOperations::CONTROLE))
            ->filterTermByFieldValue('service_operation.keyword', PreControlePosLineActivationOperations::CONTROLE)
            ->brother($aggsPlanPos);

        $aggsOperator = (new ElasticsearchAggregationStructure('operators'))
            ->terms('service_operator.keyword')
            ->nest($aggsControle);

        $aggsUser = (new ElasticsearchAggregationStructure('users'))
            ->terms('user_cpf.keyword', ['size' => '10000'])
            ->nest($aggsOperator);

        $aggsPointsOfSale = (new ElasticsearchAggregationStructure('pointsOfSale'))
            ->terms('pointofsale_cnpj.keyword', ['size' => '10000'])
            ->nest($aggsUser);

        return (new ElasticsearchQueryBuilder())
            ->size(0)
            ->aggregations($aggsPointsOfSale)
            ->get();
    }
}
