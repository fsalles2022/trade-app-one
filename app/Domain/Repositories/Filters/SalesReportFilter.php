<?php


namespace TradeAppOne\Domain\Repositories\Filters;

use Carbon\Carbon;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;

class SalesReportFilter extends BaseFilters
{
    public $query;

    public function __construct(?ElasticsearchQueryBuilder $query = null)
    {
        $this->query = $query ?? new ElasticsearchQueryBuilder();
    }

    public function serviceTransaction(string $value)
    {
        $this->query->where('service_servicetransaction.keyword', $value);
        return $this;
    }

    public function startDate(string $value)
    {
        $startDate = Carbon::parse($value)->toIso8601String();

        $this->query->where('created_at', "[{$startDate} TO *]");
        return $this;
    }

    public function endDate(string $value)
    {
        $endDate = Carbon::parse($value)->toIso8601String();

        $this->query->where('created_at', "[* TO {$endDate}]");
        return $this;
    }

    public function cpfSalesman(string $value)
    {
        $this->query->where('user_cpf.keyword', $value);
        return $this;
    }

    public function pointOfSaleCnpj(array $values)
    {
        $this->query->whereIn('pointofsale_cnpj.keyword', $values, 'OR');
        return $this;
    }

    public function pointOfSaleSlug(array $values)
    {
        $this->query->whereIn('pointofsale_slug.keyword', $values, 'OR');
        return $this;
    }

    public function getQuery(): ElasticsearchQueryBuilder
    {
        return $this->query;
    }
}
