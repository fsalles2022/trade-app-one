<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Support\Collection;

interface ImportSource
{
    public function execute(array $options): Collection;
}
