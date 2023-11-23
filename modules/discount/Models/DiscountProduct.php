<?php

namespace Discount\Models;

use TradeAppOne\Domain\Models\Tables\BaseModel;

/**
 * @property string operator
 * @property string operation
 * @property string label
 */

class DiscountProduct extends BaseModel
{
    protected $fillable = ['filterMode', 'operator', 'operation', 'product', 'promotion', 'discountId', 'label'];

    public function rules(): array
    {
        return [
            'filterMode' => 'nullable|sometimes',
            'operator'   => 'required|string',
            'operation'  => 'required|string',
            'product'    => 'sometimes|required|string',
            'promotion'  => 'sometimes|required|string',
        ];
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discountId');
    }
}
