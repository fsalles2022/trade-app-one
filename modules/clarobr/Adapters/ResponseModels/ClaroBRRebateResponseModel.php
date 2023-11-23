<?php

namespace ClaroBR\Adapters\ResponseModels;

class ClaroBRRebateResponseModel
{
    public $model;
    public $label;
    public $priceWith;
    public $priceWithout;
    public $penalty;

    public function toArray()
    {
        $attributes = get_object_vars($this);
        return array_filter($attributes);
    }
}
