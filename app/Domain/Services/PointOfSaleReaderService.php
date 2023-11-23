<?php

namespace TradeAppOne\Domain\Services;

use TradeAppOne\Domain\Exportables\PointOfSaleExport;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\PointOfSaleRepositoryReader;

class PointOfSaleReaderService
{
    protected $reader;

    public function __construct(PointOfSaleRepositoryReader $reader)
    {
        $this->reader = $reader;
    }

    public function export(User $user, $parameters): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pointOfSale = $this->reader->getPointOfSaleExport($user, $parameters);

        return (new PointOfSaleExport(collect($pointOfSale)))->writeCsv();
    }
}
