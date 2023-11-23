<?php


namespace Outsourced\ViaVarejo\Adapters\Request;

interface PayloadAdapterInterface
{
    public function plan(): array;
    public function toArray(): array;
}
