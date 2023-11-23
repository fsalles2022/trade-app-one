<?php

namespace TradeAppOne\Domain\Models\Tables;

/**
 * @property ObjectId _id
 */
class PasswordReset extends BaseModel
{
    protected $table = 'passwordResets';

    protected $fillable = [
        'userId',
        'pointsOfSaleId',
        'status',
        'createdAt',
        'managerId'
    ];

    public function pointOfSale()
    {
        return $this->belongsTo(PointOfSale::class, 'pointsOfSaleId', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}
