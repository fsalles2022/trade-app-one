<?php

namespace TradeAppOne\Domain\Models\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use TradeAppOne\Exceptions\SystemExceptions\ServiceOptionsExceptions;

class ServiceOption extends BaseModel
{
    public const CARTEIRIZACAO        = 'CARTEIRIZACAO';
    public const CONTROLE_FACIL_LIO   = 'CONTROLE_FACIL_LIO';
    public const CLARO_PRE_CHIP_COMBO = 'CLARO_PRE_CHIP_COMBO';
    public const AUTENTICA            = 'AUTENTICA';

    protected $table    = 'serviceOptions';
    protected $fillable = [
        'action'
    ];

    protected $hidden = [
        'pivot',
        'deletedAt',
        'updatedAt',
        'createdAt'
    ];

    public static function findByPointOfSale(PointOfSale $pointOfSale, array $filters = []): Collection
    {
        $availableServices = AvailableService::query()
            ->whereHas('service', static function (Builder $serviceQuery) use ($filters) {
                $serviceQuery->where($filters);
            })
            ->where(static function (Builder $builder) use ($pointOfSale) {
                $builder->where('pointOfSaleId', $pointOfSale->id)
                    ->orWhere('networkId', $pointOfSale->network->id);
            })
            ->get();

        $preference = $availableServices->where('networkId', '=', null)->first()
            ?? $availableServices->first();

        return $preference === null
            ? collect()
            : $preference->options;
    }

    public static function findByAction(string $action): ServiceOption
    {
        $serviceOption = self::where('action', $action)->first();
        throw_unless($serviceOption, ServiceOptionsExceptions::actionServiceOptionsNotFound());

        return $serviceOption;
    }
}
