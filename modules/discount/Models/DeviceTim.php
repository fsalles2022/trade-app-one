<?php

declare(strict_types=1);

namespace Discount\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use TradeAppOne\Domain\Models\Tables\BaseModel;

/**
 * @property integer id
 * @property string label
 * @property string model
 * @property string brand
 * @property string externalIdentifier
 * @property float price
 */

class DeviceTim extends BaseModel
{
    /** @var string */
    protected $table = 'devices_outsourced_tim';

    /** @var string[] */
    protected $fillable = [
        'label',
        'model',
        'brand',
        'price',
        'externalIdentifier',
    ];

    /** @var string[] */
    protected $dates = [
        self::CREATED_AT,
        self::UPDATED_AT,
        self::DELETED_AT,
    ];

    /** @var string[] */
    protected $casts = [
        'price' => 'float(8,2)',
    ];

    /** @return mixed[] */
    public function rules(): array
    {
        return [
            'label'               => 'required|string',
            'model'               => 'required|string',
            'brand'               => 'sometimes|required|string',
            'price'               => 'required|numeric',
            'externalIdentifier'  => 'sometimes|required|string'
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(DiscountProductTim::class, 'discounts_products_devices_tim', 'deviceId', 'discountProductId')->withPivot(['id', 'discount']);
    }
}
