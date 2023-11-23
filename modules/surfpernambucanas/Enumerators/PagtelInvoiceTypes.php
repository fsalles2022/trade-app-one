<?php

declare(strict_types=1);

namespace SurfPernambucanas\Enumerators;

final class PagtelInvoiceTypes
{
    public const CARTAO_CREDITO = 'Credito';

    /** @var string[] */
    public const FLAGS = [
        self::CARTAO_CREDITO => 'C',
    ];
}
