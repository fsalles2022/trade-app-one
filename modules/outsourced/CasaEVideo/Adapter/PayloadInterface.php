<?php

declare(strict_types=1);

namespace Outsourced\CasaEVideo\Adapter;

interface PayloadInterface
{
    public function toArray(): array;
}
