<?php

namespace FastShop\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use TradeAppOne\Domain\Models\Tables\BaseModel;
use TradeAppOne\Domain\Models\Tables\Service;

/**
 * @property integer id
 * @property string code
 * @property string title
 * @property integer areaCode
 * @property integer loyaltyMonths
 * @property float price
 * @property Service service
 * @property integer internet
 * @property integer minutes
 * @property mixed extras
 * @property mixed original
 */
// TODO Talvez haverá necessidade para refatorar e mover para App/Domain caso seja utilizado no contexto geral.
class Product extends BaseModel
{
    protected $table = 'products';

    protected $fillable = [
        'code',
        'title',
        'areaCode',
        'loyaltyMonths',
        'price',
        'serviceId',
        'internet',
        'minutes',
        'extras',
        'original'
    ];

    protected $hidden = [
        'updatedAt',
        'deletedAt'
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'serviceId');
    }
}
