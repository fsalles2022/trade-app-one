<?php

namespace ClaroBR\Models;

use TradeAppOne\Domain\Models\Plan;

class PlanClaro extends Plan
{
    public $promotion;
    public $mode;
    public $nome;
    public $operatorCode;

    public function toArray()
    {
        return [
            "product" => $this->product,
            "label" => $this->label,
            "nome" => $this->nome,
            "price" => $this->price,
            "operator" => $this->operator,
            "operation" => $this->operation,
            "operatorCode" => $this->operatorCode,
            "areaCode" => $this->areaCode,
            "invoiceTypes" => $this->invoiceTypes,
            "mode" => $this->mode,
            "promotion" => $this->promotion ? $this->promotion->toArray() : null
        ];
    }
}
