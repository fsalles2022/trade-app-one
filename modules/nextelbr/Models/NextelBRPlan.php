<?php

namespace NextelBR\Models;

use Illuminate\Contracts\Support\Arrayable;

class NextelBRPlan implements Arrayable
{
    public $offer;
    public $product;
    public $label;
    public $table;
    public $price;
    public $adhesionValue;

    public function toArray()
    {
        return compact('thi');
    }
}
