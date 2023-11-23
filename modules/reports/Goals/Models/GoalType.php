<?php

namespace Reports\Goals\Models;

use TradeAppOne\Domain\Models\Tables\BaseModel;
use TradeAppOne\Domain\Models\Tables\Network;

class GoalType extends BaseModel
{
    protected $table = 'goalsTypes';

    protected $fillable = [
        'id',
        'slug',
        'label'
    ];

    protected $hidden = [
        'pivot',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    public function network()
    {
        return $this->belongsToMany(Network::class, 'network_goalsTypes', 'goalTypeId', 'networkId');
    }

    public function goals()
    {
        return $this->hasMany(Goal::class, 'goalTypeId');
    }
}
