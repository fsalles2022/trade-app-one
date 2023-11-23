<?php

namespace TradeAppOne\Domain\Models\Collections;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TradeAppOne\Domain\Models\Tables\PointOfSale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Models\Tables\UserAuthAlternates;

/**
 * @property User user
 * @property PointOfSale pointOfSale
 * @property string saleTransaction
 * @property string source
 * @property boolean isPreSale
 * @property string channel
 * @property float total
 * @property Collection services
 * @property UserAuthAlternates userAlternate
 * @property Carbon createdAt
 */
class Sale extends BaseModel
{
    protected $connection = 'mongodb';

    protected $fillable = [
        'user',
        'pointOfSale',
        'channel',
        'saleTransaction',
        'total',
        'source',
        'isPreSale',
        'burned',
        'userAlternate'
    ];

    protected $casts = [
        'isPreSale' => 'boolean',
    ];

    public function services()
    {
        return $this->embedsMany(Service::class);
    }

    public function setTransactionNumber()
    {
        $this->attributes['saleTransaction'] = ServiceTransactionGenerator::generate();
        return $this->attributes;
    }
}
