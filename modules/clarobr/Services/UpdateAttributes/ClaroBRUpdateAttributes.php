<?php

namespace ClaroBR\Services\UpdateAttributes;

use Illuminate\Support\Collection;

interface ClaroBRUpdateAttributes
{
    public function update(array $options): ?Collection;
}
