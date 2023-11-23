<?php

declare(strict_types=1);

namespace Outsourced\Pernambucanas\Adapters;

interface PayloadInterface
{
    public function toArray(): array;
}
