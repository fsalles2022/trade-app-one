<?php

namespace Reports\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Repositories\Collections\RefusedSaleRepository;

class RefusedSaleReportService
{
    public function filter(array $filters): LengthAwarePaginator
    {
        return RefusedSaleRepository::getByFilter(array_filter($filters))->paginate(10);
    }

    public function getRefusedSalesExport(array $filters): StreamedResponse
    {
        $data = RefusedSaleRepository::getToExport($filters);
        return CsvHelper::exportDataToCsvFile($data, 'relatorio_negados');
    }
}
