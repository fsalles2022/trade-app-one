<?php

namespace TradeAppOne\Domain\Models\Tables;

use Discount\Models\DeviceDiscount;

/**
 * @property string SKU
 * @property string model
 * @property string label
 * @property string brand
 * @property string color
 * @property string storage
 * @property Network network
 */
class DeviceOutSourced extends BaseModel
{
    protected $table = 'devices_outsourced';

    protected $fillable = [
        'sku',
        'model',
        'label',
        'price',
        'brand',
        'color',
        'storage',
        'networkId'
    ];

    public function network()
    {
        return $this->belongsTo(Network::class, 'networkId');
    }

    public function discounts()
    {
        return $this->hasMany(DeviceDiscount::class, 'deviceId');
    }
}
