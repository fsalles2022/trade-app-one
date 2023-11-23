<?php

namespace TradeAppOne\Domain\Models\Tables;

/**
 * @property integer id
 * @property User user
 * @property PointOfSale pointOfSale
 */
class UserPendingRegistration extends BaseModel
{
    protected $table = 'userThirdPartyRegistrations';

    protected $fillable = ['userId', 'pointOfSaleId', 'done', 'operator', 'log'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function pointOfSale()
    {
        return $this->belongsTo(PointOfSale::class, 'pointOfSaleId', 'id');
    }
}
