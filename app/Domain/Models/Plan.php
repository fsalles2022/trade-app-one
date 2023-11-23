<?php

namespace TradeAppOne\Domain\Models;

use Illuminate\Contracts\Support\Arrayable;

class Plan implements Arrayable
{
    public $product;
    public $label;
    public $price;
    public $original;
    public $details;
    public $operator;
    public $operation;
    public $invoiceTypes;
    public $areaCode;
    public $dependents;

    public function __construct(
        string $product,
        string $label,
        float $price,
        array $original
    ) {
        $this->product  = $product;
        $this->label    = $label;
        $this->price    = $price;
        $this->original = $original;
    }

    public function toArray()
    {
        return [
            "product" => $this->product,
            "label" => $this->label,
            "price" => $this->price,
            "operator" => $this->operator,
            "operation" => $this->operation,
            "areaCode" => $this->areaCode,
            "invoiceTypes" => $this->invoiceTypes,
            "original" => $this->original,
            'dependents' => $this->dependents
        ];
    }
}
