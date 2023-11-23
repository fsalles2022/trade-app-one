<?php

namespace TradeAppOne\Events;

use TradeAppOne\Domain\Adapters\Adapter;

class PreAnalysisEvent
{
    public $data;

    public function __construct(?Adapter $data)
    {
        $this->data = $data;
    }
}
