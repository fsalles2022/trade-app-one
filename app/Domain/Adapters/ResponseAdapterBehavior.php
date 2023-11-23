<?php

namespace TradeAppOne\Domain\Adapters;

interface ResponseAdapterBehavior
{
    public function getStatus();

    public function adapt();

    public function getAdapted(): array;
}
