<?php

namespace TradeAppOne\Domain\Repositories\Tables;

use TradeAppOne\Domain\Models\Tables\ImportSale;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class SaleImportRepository extends BaseRepository
{
    protected $model = ImportSale::class;

    public function findImportSale(array $attributes): ?ImportSale
    {
        if ($this->hasPrimaryAndSecondaryId($attributes)) {
            return $this->createModel()->where([
                'service_operator_pid' => $attributes['service_operator_pid'],
                'service_operator_sid' => $attributes['service_operator_sid'],
                'service_operator'     => $attributes['service_operator']
            ])->first();
        } elseif ($this->hasOnlyPrimary($attributes)) {
            return $this->createModel()->where([
                'service_operator_pid' => $attributes['service_operator_pid'],
                'service_operator'     => $attributes['service_operator']
            ])->first();
        }
    }

    private function hasPrimaryAndSecondaryId(array $attributes): bool
    {
        return isset($attributes['service_operator_sid']) &&
            isset($attributes['service_operator_pid']) &&
            isset($attributes['service_operator']);
    }

    private function hasOnlyPrimary(array $attributes): bool
    {
        return isset($attributes['service_operator_pid']) &&
            empty($attributes['service_operator_sid']) &&
            isset($attributes['service_operator']);
    }
}
