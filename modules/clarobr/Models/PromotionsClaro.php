<?php

namespace ClaroBR\Models;

use Illuminate\Contracts\Support\Arrayable;

class PromotionsClaro implements Arrayable
{
    public $id;
    public $label;
    public $price;
    public $mode;
    public $loyalty;
    public $penalty;
    public $needDevice;


    public function toArray()
    {
        return [
            "id" => $this->id,
            "label" => $this->label,
            "price" => $this->price,
            "mode" => $this->mode,
            "loyalty" => $this->loyalty,
            "penalty" => $this->penalty,
            "needDevice" => $this->needDevice,
        ];
    }
}
