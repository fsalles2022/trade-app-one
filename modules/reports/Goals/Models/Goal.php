<?php

namespace Reports\Goals\Models;

use TradeAppOne\Domain\Models\Tables\BaseModel;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

/**
 * @property int goal
 * @property int month
 * @property int year
 */
class Goal extends BaseModel
{
    protected $fillable = ['year', 'month', 'goal', 'pointOfSaleId', 'goalTypeId'];

    public function pointOfSale()
    {
        return $this->belongsTo(PointOfSale::class, 'pointOfSaleId', 'id');
    }

    public function goalType()
    {
        return $this->belongsTo(GoalType::class, 'goalTypeId', 'id');
    }

    public function rules(): array
    {
        return [
            'year'  => 'after_or_equal:' . date('Y') . '|date_format:"Y"',
            'month' => 'numeric|between:1,12',
            'goal'  => 'numeric'
        ];
    }
}
