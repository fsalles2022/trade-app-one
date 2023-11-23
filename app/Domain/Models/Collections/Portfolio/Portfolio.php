<?php

namespace TradeAppOne\Domain\Models\Collections\Portfolio;

use TradeAppOne\Domain\Models\Collections\BaseModel;

class Portfolio extends BaseModel
{
    protected $fillable   = ['operator', 'operation', 'details'];
    protected $connection = 'mongodb';
    public function products()
    {
        return $this->embedsMany(Product::class);
    }
}
