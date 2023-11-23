<?php

namespace TradeAppOne\Domain\Models\Tables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TradeAppOne\Domain\Enumerators\Operations;

class Service extends Model
{
    use SoftDeletes;

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';
    public const DELETED_AT = 'deletedAt';

    public static $snakeAttributes = false;

    protected $dates    = ['deletedAt'];
    protected $table    = 'services';
    protected $fillable = [
        'sector',
        'operator',
        'operation',
        'label',
    ];

    protected $hidden = [
        'updatedAt',
        'deletedAt'
    ];

    public function availableServices()
    {
        return $this->hasMany(AvailableService::class, 'serviceId');
    }

    public function pointsOfSale()
    {
        return $this->belongsToMany(PointOfSale::class, 'availableServices', 'serviceId', 'pointOfSaleId');
    }

    public function networks()
    {
        return $this->belongsToMany(Network::class, 'availableServices', 'serviceId', 'networkId');
    }

    public function isOperatorClaro(): bool
    {
        return $this->attributes['operator'] === Operations::CLARO;
    }
}
