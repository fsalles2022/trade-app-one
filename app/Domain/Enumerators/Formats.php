<?php

namespace TradeAppOne\Domain\Enumerators;

final class Formats
{
    const DATE  = 'Y-m-d';
    const NAMES = 'regex:/^[\pL\s\-]+$/u';
    const CPF   = 'size:11|numeric';
}
