<?php

namespace Core\Charts\Model;

use TradeAppOne\Domain\Models\Tables\BaseModel;

class Chart extends BaseModel
{
    protected $table = 'charts';

    protected $fillable = [
      'name',
      'slug',
      'description',
      'type',
      'createdAt',
      'updatedAt'
    ];

    public function chartRole()
    {
        return $this->hasMany(ChartRole::class, 'chartId');
    }
}
