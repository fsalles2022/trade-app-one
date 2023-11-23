<?php

namespace TradeAppOne\Domain\Models\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AvailableService extends BaseModel
{
    protected $table = 'availableServices';

    protected $fillable = [
        'networkId',
        'pointOfSaleId',
        'serviceId'
    ];

    protected $hidden = [
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'serviceId');
    }

    public function pointOfSale(): BelongsTo
    {
        return $this->belongsTo(PointOfSale::class, 'pointOfSaleId');
    }

    public function network(): BelongsTo
    {
        return $this->belongsTo(Network::class, 'networkId');
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(ServiceOption::class, 'services_serviceOptions', 'availableServiceId', 'optionId');
    }

    public function servicesServiceOptions(): HasOne
    {
        return $this->hasOne(ServicesServiceOption::class, 'availableServiceId', 'id');
    }

    public static function findByPointOfSale(PointOfSale $pointOfSale, array $operations): Builder
    {
        return self::whereHas('service', static function (Builder $serviceQuery) use ($operations) {
                $serviceQuery->whereIn('operation', array_wrap($operations));
        })->where('pointOfSaleId', '=', $pointOfSale->id);
    }

    public static function findByNetwork(Network $network, array $operations)
    {
        return self::whereHas('service', static function (Builder $serviceQuery) use ($operations) {
            $serviceQuery->whereIn('operation', array_wrap($operations));
        })->where('networkId', '=', $network->id);
    }
}
