<?php

namespace TradeAppOne\Domain\Enumerators\Operations;

use Uol\Models\UolCurso;

final class UolOperations
{
    const UOL_STANDARD     = 'UOL_STANDARD';
    const UOL_PLUS         = 'UOL_PLUS';
    const UOL_PROFESSIONAL = 'UOL_PROFESSIONAL';

    const OPERATORS = [
        self::UOL_STANDARD                                  => UolCurso::class,
        self::UOL_PLUS                                      => UolCurso::class,
        self::UOL_PROFESSIONAL                              => UolCurso::class,
    ];
}
