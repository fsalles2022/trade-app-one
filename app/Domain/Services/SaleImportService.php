<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Tables\ImportSale;
use TradeAppOne\Domain\Repositories\Tables\SaleImportRepository;

class SaleImportService extends BaseService
{
    /**
     * @var SaleImportRepository
     */
    protected $repository;

    public function __construct(SaleImportRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createMany(Collection $sales): Collection
    {
        $collection = collect();
        foreach ($sales as $sale) {
            $record = $this->createOrUpdate($sale);
            $collection->push($record);
        }
        return $collection;
    }

    public function createOrUpdate(array $attributes): array
    {
        $sale = $this->repository->findImportSale($attributes);

        if ($sale instanceof ImportSale) {
            return ['action' => 'update', 'record' => $this->repository->update($sale, $attributes)];
        }

        return ['action' => 'create', 'record' => $this->repository->create($attributes)];
    }
}
