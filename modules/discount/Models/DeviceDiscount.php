<?php

namespace Discount\Models;

use TradeAppOne\Domain\Models\Tables\BaseModel;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;

/**
 * @property integer id
 * @property double discount
 * @property string price
 * @property Discount discountEntity
 */
class DeviceDiscount extends BaseModel
{
    protected $table = 'devices_discounts';

    protected $fillable = [
        'price',
        'discount',
        'deviceId',
        'discountId'
    ];

    protected $hidden = [
        'id',
        'discountId',
        'deviceId',
        'updatedAt',
        'createdAt',
        'deletedAt'
    ];

    public function device()
    {
        return $this->belongsTo(DeviceOutSourced::class, 'deviceId');
    }

    public function discountEntity()
    {
        return $this->belongsTo(Discount::class, 'discountId');
    }
}
