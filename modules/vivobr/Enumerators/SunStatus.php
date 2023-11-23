<?php

namespace VivoBR\Enumerators;

use TradeAppOne\Domain\Enumerators\ServiceStatus;

final class SunStatus
{
    const ACCEPTED = ['AGUARDANDO'];

    const APPROVED = ['APROVADO', 'PALITAGEM'];

    const REJECTED = [
        'REPROVADO',
        'REPROVADO_INDISPONIBILIDADE',
        'REPROVADO_SCORE_NEGADO_SERASA',
        'REPROVADO_SCORE_NEGADO_VIVO',
        'REPROVADO_DESISTENCIA',
    ];

    const CANCELED = ['DESISTENCIA', 'CANCELADO'];

    const ORIGINAL_STATUS = [
        'REPROVADO'                     => ServiceStatus::REJECTED,
        'REPROVADO_INDISPONIBILIDADE'   => ServiceStatus::REJECTED,
        'REPROVADO_SCORE_NEGADO_SERASA' => ServiceStatus::REJECTED,
        'REPROVADO_SCORE_NEGADO_VIVO'   => ServiceStatus::REJECTED,
        'REPROVADO_DESISTENCIA'         => ServiceStatus::REJECTED,
        'DESISTENCIA'                   => ServiceStatus::CANCELED,
        'CANCELADO'                     => ServiceStatus::CANCELED,
        'AGUARDANDO'                    => ServiceStatus::ACCEPTED,
        'PALITAGEM'                     => ServiceStatus::APPROVED,
        'APROVADO'                      => ServiceStatus::APPROVED,
    ];
}
