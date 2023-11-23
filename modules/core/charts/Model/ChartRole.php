<?php

namespace Core\Charts\Model;

use TradeAppOne\Domain\Models\Tables\BaseModel;
use TradeAppOne\Domain\Models\Tables\Role;

class ChartRole extends BaseModel
{
    protected $table = 'chart_roles';

    protected $fillable = [
        'size',
        'order',
        'chartId',
        'roleId'
    ];

    public function chart()
    {
        return $this->belongsTo(Chart::class, 'chartId');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'roleId');
    }
}
