<?php

namespace TradeAppOne\Domain\Models\Collections\Portfolio;

use TradeAppOne\Domain\Models\Collections\BaseModel;

class Product extends BaseModel
{
    protected $fillable   = ['id', 'price', 'details', 'area'];
    protected $connection = 'mongodb';

    public function operator()
    {
        return $this->belongsTo(Portfolio::class);
    }
}
