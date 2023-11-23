<?php

namespace Uol\Enumerators;

use TradeAppOne\Domain\Enumerators\Operations\UolOperations;

final class UolPlansEnum
{
    const UOL_STANDARD     = '1';
    const UOL_PLUS         = '2';
    const UOL_PROFESSIONAL = '5';

    const PLANS_AVAILABLE = [
        self::UOL_STANDARD,
        self::UOL_PLUS,
        self::UOL_PROFESSIONAL
    ];

    const PRICES = [
        self::UOL_STANDARD      => '49.00',
        self::UOL_PLUS          => '79.90',
        self::UOL_PROFESSIONAL  => '199.00'
    ];

    const NAME = [
        self::UOL_STANDARD     => UolOperations::UOL_STANDARD,
        self::UOL_PLUS         => UolOperations::UOL_PLUS,
        self::UOL_PROFESSIONAL => UolOperations::UOL_PROFESSIONAL
    ];

    const LABEL = [
        self::UOL_STANDARD     => 'Standard',
        self::UOL_PLUS         => 'Plus',
        self::UOL_PROFESSIONAL => 'Inglês, Concursos e Profissionalizantes'
    ];

    const DETAILS = [
        self::UOL_STANDARD => [
            'Cursos Básicos com até 30 horas',
            'Auxiliar de laboratório',
            'Bolos artísticos',
            'Como enfrentar o stress'
        ],
        self::UOL_PLUS => [
            'Cursos Básicos com até 60 horas',
            'Pesquisa de mercado', 'Gestão de equipes'
        ],
        self::UOL_PROFESSIONAL => [
            'Melhore seus conhecimentos na sua área de trabalho com planos de estudos, cursos e inglês',
            'Carga horária de até 600 horas'
        ]
    ];
}
