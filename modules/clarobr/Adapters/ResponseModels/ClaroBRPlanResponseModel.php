<?php

namespace ClaroBR\Adapters\ResponseModels;

class ClaroBRPlanResponseModel
{
    public $label;
    public $product;
    public $price;
    public $promotion = null;
    public $slug;

    public function toArray()
    {
        $attributes = get_object_vars($this);
        unset($attributes['slug']);
        return array_filter($attributes);
    }
}
