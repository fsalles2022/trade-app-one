<?php

namespace ClaroBR\Enumerators;

use TradeAppOne\Domain\Enumerators\ServiceStatus;

class SivStatus
{
    public const PENDENTE_M4U = 'PENDENTE_M4U';
    public const CANCELED_STATUS = 'CANCELADO';
    public const APPROVED_STATUS = 'APROVADO';

    public const ACCEPTED = [
        'ENVIADO',
        'ANALISE_RCV',
        'ATIVADO',
        'CONTINGENCIA',
        'ENVIO_PENDENTE',
        'PENDENTE_M4U',
        'ENVIADO_BKO'
    ];

    public const CANCELED = ['DESISTÊNCIA', 'CANCELADO'];
    public const APPROVED = ['APROVADO'];
    public const REJECTED = ['REPROVADO', 'INEXISTENTE', 'ATIVO SEM ACEITE'];

    public const ORIGINAL_STATUS = [
        'APROVADO'          => ServiceStatus::APPROVED,
        'REPROVADO'         => ServiceStatus::REJECTED,
        'AGUARDANDO'        => ServiceStatus::REJECTED,
        'INEXISTENTE'       => ServiceStatus::REJECTED,
        'ENVIO_PENDENTE'    => ServiceStatus::ACCEPTED,
        'ATIVO SEM ACEITE'  => ServiceStatus::ACCEPTED,
        'ATIVADO'           => ServiceStatus::ACCEPTED,
        'ENVIADO'           => ServiceStatus::ACCEPTED,
        'ANALISE_RCV'       => ServiceStatus::ACCEPTED,
        'DESISTÊNCIA'       => ServiceStatus::CANCELED,
        'CANCELADO'         => ServiceStatus::CANCELED,
        'PENDENTE_BKO'      => ServiceStatus::SUBMITTED,
        'ENVIADO_BKO'       => ServiceStatus::ACCEPTED,
        'INSTALADO'         => ServiceStatus::APPROVED
    ];

    public const ORIGINAL_STATUS_ANALYTICAL = [
        'APROVADO' => 'APROVADO',
        'REPROVADO' => 'REPROVADO',
        'AGUARDANDO' => 'AGUARDANDO',
        'INEXISTENTE' => 'INEXISTENTE',
        'ENVIO_PENDENTE' => 'ENVIO_PENDENTE',
        'ATIVO SEM ACEITE' => 'ATIVO SEM ACEITE',
        'ATIVADO' => 'ANALISE_RCV',
        'ENVIADO' => 'ANALISE_RCV',
        'ANALISE_RCV' => 'ANALISE_RCV',
        'DESISTÊNCIA' => 'DESISTÊNCIA',
        'CANCELADO' => 'CANCELADO'
    ];
}
