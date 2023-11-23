<?php


namespace Recommendation\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use TradeAppOne\Domain\Models\Tables\BaseModel;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

/**
 * @property integer id
 * @property string name
 * @property string statusCode
 * @property string registration
 * @property PointOfSale pointOfSaleId
 */
class Recommendation extends BaseModel
{
    protected $table = 'recommendations';

    protected $fillable = [
        'name',
        'statusCode',
        'registration',
        'pointOfSaleId',
    ];

    protected $hidden = ['deletedAt'];

    public function pointOfSale(): BelongsTo
    {
        return $this->belongsTo(PointOfSale::class, 'pointOfSaleId');
    }
}
