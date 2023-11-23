<?php

namespace TradeAppOne\Exports\Operators;

use Illuminate\Foundation\Bus\PendingDispatch;

interface RegisterMailToOperator
{
    public function build(string $networks): ?PendingDispatch;
}
