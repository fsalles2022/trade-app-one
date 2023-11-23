<?php

namespace Buyback\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use TradeAppOne\Domain\Models\Tables\BaseModel;
use TradeAppOne\Domain\Models\Tables\Device;
use TradeAppOne\Domain\Models\Tables\Network;

/**
 * @property integer id
 * @property Network network
 * @property Device device
 * @property string sku
 */
class DevicesNetwork extends BaseModel
{
    protected $table = 'devices_network';

    protected $fillable = [
        'deviceId',
        'networkId',
        'sku',
        'isPreSale'
    ];

    protected $casts = [
        'isPreSale' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        $whenNotExistsSkuUseDeviceId = function (DevicesNetwork $model) {
            $model->sku = filled($model->sku) ? $model->sku : $model->device->id;
        };

        self::saving($whenNotExistsSkuUseDeviceId);
    }

    public function network(): BelongsTo
    {
        return $this->belongsTo(Network::class, 'networkId');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'deviceId');
    }

    public function toMongoAggregation(string $imei): array
    {
        $sku = filled($this->sku) ? $this->sku : strval($this->device->id);

        return [
            'id' => $this->device->id,
            'model' => $this->device->model,
            'brand' => $this->device->brand,
            'color' => $this->device->color,
            'storage'  => $this->device->storage,
            'label' => $this->device->label,
            'imei' => $imei,
            'sku' => $sku,
            'updatedAt' => $this->device->updatedAt,
            'createdAt' => $this->device->createdAt,
            'deletedAt' => $this->device->deletedAt
        ];
    }
}
