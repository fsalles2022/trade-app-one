<?php

declare(strict_types=1);

namespace Discount\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer id
 * @property integer discountProductId
 * @property integer deviceId
 * @property double discount
 **/
class DiscountProductDeviceTim extends Model
{
    protected $fillable = [
        'discount',
        'discountProductId',
        'deviceId',
    ];

    protected $table = 'discounts_products_devices_tim';

    public function device(): BelongsTo
    {
        return $this->belongsTo(DeviceTim::class, 'deviceId', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(DiscountProductTim::class, 'discountProductId', 'id');
    }
}
