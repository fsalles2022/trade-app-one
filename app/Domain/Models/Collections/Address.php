<?php

namespace TradeAppOne\Domain\Models\Collections;

class Address extends BaseModel
{
    protected $fillable = [
        'zipCode',
        'local',
        'neighborhood',
        'state',
        'number',
        'city',
        'district',
        'complement',
        'latitude',
        'longitude'
    ];
}
