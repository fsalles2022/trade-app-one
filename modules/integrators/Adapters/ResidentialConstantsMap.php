<?php


namespace Integrators\Adapters;

use ClaroBR\Enumerators\SivStatus;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

class ResidentialConstantsMap
{
    public const RESIDENTIAL_STATUS = [
        Operations::CLARO_CONTROLE_BOLETO => self::CUSTOM_STATUS_MAPPER,
        Operations::CLARO_CONTROLE_FACIL => SivStatus::ORIGINAL_STATUS,
        'PRE_PAGO' => SivStatus::ORIGINAL_STATUS,
        'POS_PAGO'=> self::CUSTOM_STATUS_MAPPER,
        'BANDA_LARGA'=> SivStatus::ORIGINAL_STATUS,
        'VOZ_DADOS' => SivStatus::ORIGINAL_STATUS,
        'CONTROLE' => SivStatus::ORIGINAL_STATUS,
        'DADOS' => SivStatus::ORIGINAL_STATUS,
        'PONTO_ADICIONAL' => SivStatus::ORIGINAL_STATUS,
        Operations::CLARO_RESIDENCIAL => SivStatus::ORIGINAL_STATUS,
        Operations::CLARO_TELEVISAO => SivStatus::ORIGINAL_STATUS,
        Operations::CLARO_FIXO => SivStatus::ORIGINAL_STATUS,
        Operations::CLARO_TV_PRE => SivStatus::ORIGINAL_STATUS,
    ];

    private const CUSTOM_STATUS_MAPPER = [
        'APROVADO'         => ServiceStatus::APPROVED,
        'AGUARDANDO'       => ServiceStatus::PENDING_SUBMISSION,
        'REPROVADO'        => ServiceStatus::REJECTED,
        'INEXISTENTE'      => ServiceStatus::REJECTED,
        'ENVIO_PENDENTE'   => ServiceStatus::ACCEPTED,
        'ATIVO SEM ACEITE' => ServiceStatus::ACCEPTED,
        'ATIVADO'          => ServiceStatus::ACCEPTED,
        'ENVIADO'          => ServiceStatus::ACCEPTED,
        'ANALISE_RCV'      => ServiceStatus::ACCEPTED,
        'DESISTÊNCIA'      => ServiceStatus::CANCELED,
        'CANCELADO'        => ServiceStatus::CANCELED,
        'PENDENTE_BKO'     => ServiceStatus::SUBMITTED,
        'ENVIADO_BKO'      => ServiceStatus::ACCEPTED,
        'INSTALADO'        => ServiceStatus::APPROVED
    ];

    public const SIV_MODES = [
        'ATIVACAO' => Modes::ACTIVATION,
        'MIGRACAO' => Modes::MIGRATION,
        'PORTADO' => Modes::PORTABILITY,
    ];
}
