<?php

declare(strict_types=1);

namespace Discount\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use TradeAppOne\Domain\Models\Tables\BaseModel;

/**
 * @property integer id
 * @property string label
 * @property string externalIdentifier
 */

class DiscountProductTim extends BaseModel
{
    /** @var string */
    protected $table = 'discount_products_tim';

    /** @var string[] */
    protected $fillable = [
        'label',
        'externalIdentifier',
    ];

    /** @var string[] */
    protected $dates = [
        self::CREATED_AT,
        self::UPDATED_AT,
        self::DELETED_AT,
    ];

    /** @return mixed[] */
    public function rules(): array
    {
        return [
            'label'               => 'required|string',
            'externalIdentifier'  => 'required|string'
        ];
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(DeviceTim::class, 'discounts_products_devices_tim', 'discountProductId', 'deviceId');
    }
}
