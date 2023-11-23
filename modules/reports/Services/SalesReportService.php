<?php

namespace Reports\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Reports\Adapters\SaleReportAdapter;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;
use TradeAppOne\Domain\Repositories\Filters\SalesReportFilter;

class SalesReportService
{
    public const SALES_PER_PAGE = 10;

    private $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function filter(array $filters): array
    {
        $query = (new SalesReportFilter())
            ->apply($filters)
            ->getQuery()
            ->sort('created_at')
            ->size(self::SALES_PER_PAGE);

        $sales = $this->saleReportRepository->getFilteredByContext($query);
        return SaleReportAdapter::adapt($sales);
    }

    public function filterAndPaginate(array $filters, int $page): LengthAwarePaginator
    {
        $from = self::SALES_PER_PAGE * ($page - 1);

        $query = (new SalesReportFilter())
            ->apply($filters)
            ->getQuery()
            ->sort('created_at')
            ->from($from)
            ->size(self::SALES_PER_PAGE)
            ->get();


        $sales = $this->saleReportRepository->getFilteredByContext($query);
        return SaleReportAdapter::paginate($sales, $page, self::SALES_PER_PAGE);
    }
}
