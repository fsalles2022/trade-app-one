<?php

namespace Outsourced\ViaVarejo\Models;

use Discount\Models\Discount;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use TradeAppOne\Domain\Models\Tables\BaseModel;

class ViaVarejoCoupon extends BaseModel
{

    protected $table      = 'via_varejo_coupons';
    protected $connection = 'outsourced';

    protected $fillable = [
        'coupon',
        'campaign',
        'discountId',
    ];

    protected $hidden = [
        'updatedAt',
        'deletedAt'
    ];

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class, 'discountId');
    }
}
