<?php

namespace TradeAppOne\Domain\Models\Tables;

class ServicesServiceOption extends BaseModel
{
    protected $table = 'services_serviceOptions';

    protected $fillable = [
        'availableServiceId',
        'optionId',
    ];

    protected $hidden = [
        'createdAt',
        'updatedAt',
    ];
}
