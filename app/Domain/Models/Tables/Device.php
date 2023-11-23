<?php

namespace TradeAppOne\Domain\Models\Tables;

use Buyback\Models\Evaluation;
use Discount\Models\DeviceDiscount;
use Illuminate\Support\Collection;

/**
 * @property integer id
 * @property string model
 * @property string brand
 * @property string color
 * @property string storage
 * @property string label
 * @property string imageFront
 * @property string imageBehind
 * @property Collection discounts
 * @property string material
 * @property integer caseSize
 */
class Device extends BaseModel
{
    protected $table             = 'devices';
    public const SMARTPHONE_TYPE = 'SMARTPHONE';
    public const TABLET_TYPE     = 'TABLET';
    public const NOTEBOOK_TYPE   = 'NOTEBOOK';
    public const SMARTWATCH_TYPE = 'SMARTWATCH';
    public const DEVICE_TYPES    = [
        self::SMARTPHONE_TYPE,
        self::TABLET_TYPE,
        self::NOTEBOOK_TYPE,
    ];

    protected $fillable = [
        'model',
        'brand',
        'color',
        'storage',
        'label',
        'imageFront',
        'imageBehind',
        'type',
        'material',
        'caseSize',
    ];

    protected $hidden = [
        'updatedAt',
        'deletedAt'
    ];

    public function networks()
    {
        return $this->belongsToMany(Network::class, 'devices_network', 'deviceId', 'networkId')->withPivot(['isPreSale']);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'deviceId', '');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function discounts()
    {
        return $this->hasMany(DeviceDiscount::class, 'deviceId');
    }
}
