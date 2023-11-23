<?php

declare(strict_types=1);

namespace SurfPernambucanas\Enumerators;

final class PagtelAddressCode
{
    /** @var string[] */
    public const ADDRESS_TYPES = [
        1 => 'ACAMPAMENTO',
        2 => 'ACESSO',
        4 => 'AEROPORTO',
        5 => 'ALAMEDA',
        12 => 'AVENIDA',
        55 => 'ESTRADA',
        64 => 'FAZENDA',
        66 => 'FERROVIA',
        75 => 'LADEIRA',
        78 => 'LOTE',
        89 => 'PASSARELA',
        110 => 'RUA',
        119 => 'TREVO'
    ];
}
