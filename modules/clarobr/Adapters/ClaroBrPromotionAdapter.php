<?php

namespace ClaroBR\Adapters;

use ClaroBR\Enumerators\ClaroBrModes;
use ClaroBR\Models\PromotionsClaro;
use TradeAppOne\Domain\Adapters\Adapter;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;

class ClaroBrPromotionAdapter implements Adapter
{
    private $promotion;

    public function __construct($promotion)
    {
        $this->promotion = $promotion;
    }

    public function adapt(): ?PromotionsClaro
    {
        if (is_null($this->promotion)) {
            return null;
        }

        $id         = data_get($this->promotion, 'id');
        $name       = data_get($this->promotion, 'nome');
        $price      = data_get($this->promotion, 'valor');
        $type       = data_get($this->promotion, 'categoria');
        $loyalty    = data_get($this->promotion, 'fidelidade');
        $penalty    = data_get($this->promotion, 'multa');
        $needDevice = boolval(data_get($this->promotion, 'requer_aparelho'));
        $mode       = ConstantHelper::getValue(ClaroBrModes::class, $type);
        $active     = data_get($this->promotion, 'ativo');

        if (is_null($id)
            || is_null($name)
            || is_null($price)
            || is_null($mode)
            || is_null($loyalty)
            || is_null($penalty)
            || is_null($needDevice)
            || $active == 0) {
            return null;
        }

        $promotion             = new PromotionsClaro();
        $promotion->id         = $id;
        $promotion->label      = $name;
        $promotion->price      = $price;
        $promotion->mode       = $mode;
        $promotion->loyalty    = $loyalty;
        $promotion->penalty    = $penalty;
        $promotion->needDevice = $needDevice;

        return $promotion;
    }
}
