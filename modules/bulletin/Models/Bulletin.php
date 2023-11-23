<?php

declare(strict_types=1);

namespace Bulletin\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use TradeAppOne\Domain\Models\Tables\BaseModel;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\Role;
use TradeAppOne\Domain\Models\Tables\User;

class Bulletin extends BaseModel
{
    protected $table = 'bulletins';

    protected $fillable = [
        'title',
        'description',
        'networkId',
        'status',
        'urlImage',
        'finalDate',
        'initialDate'
    ];

    public function network(): HasOne
    {
        return $this->hasOne(Network::class, 'id');
    }

    public function role(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'bulletins_roles', 'bulletinId', 'roleId');
    }

    public function pointOfSale(): BelongsToMany
    {
        return $this->belongsToMany(PointOfSale::class, 'bulletins_pointsOfSales', 'bulletinId', 'pointOfSaleId');
    }

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bulletins_users', 'bulletinId', 'userId')
            ->as('bulletinsUsers')
            ->withPivot('seen');
    }
}
