<?php

namespace Discount\Models;

use Carbon\Carbon;
use Core\Logs\Observers\LogActionsObserver;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Outsourced\ViaVarejo\Models\ViaVarejoCoupon;
use TradeAppOne\Domain\Models\Tables\BaseModel;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;

/**
 * @property integer id
 * @property string title
 * @property string status
 * @property integer deviceId
 * @property string filterMode
 * @property double devicePrice
 * @property Carbon endAt
 * @property Carbon startAt
 * @property Collection products
 * @property Collection pointsOfSale
 *
 * @see LogActionsObserver
 */
class Discount extends BaseModel
{
    protected $fillable = [
        'title',
        'status',
        'filterMode',
        'networkId',
        'userId',
        'price',
        'startAt',
        'endAt'
    ];

    protected $table = 'discounts';

    protected $hidden = [
        'updatedAt',
        'createdAt',
        'deletedAt',
        'userId',
        'networkId',
    ];

    public function devices()
    {
        return $this->hasMany(DeviceDiscount::class, 'discountId')->with('device');
    }

    public function pointsOfSale()
    {
        return $this->belongsToMany(PointOfSale::class, 'pointsOfSale_discounts', 'discountId', 'pointOfSaleId');
    }

    public function network()
    {
        return $this->belongsTo(Network::class, 'networkId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function products()
    {
        return $this->hasMany(DiscountProduct::class, 'discountId');
    }

    public function viaVarejoCoupons(): HasMany
    {
        return $this->hasMany(ViaVarejoCoupon::class, 'discountId');
    }
}
