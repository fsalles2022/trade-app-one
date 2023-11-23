<?php

namespace OiBR\Enumerators;

use TradeAppOne\Domain\Enumerators\ServiceStatus;

final class OiBRCartaoStatus
{
    const FALHA    = 'FALHA';
    const SUCESSO  = 'SUCESSO';
    const PENDENTE = 'PENDENTE';

    const RELATED = [
        self::FALHA    => ServiceStatus::REJECTED,
        self::SUCESSO  => ServiceStatus::APPROVED,
        self::PENDENTE => ServiceStatus::ACCEPTED,
    ];

    public static function translate($status)
    {
        if (in_array($status, array_keys(self::RELATED))) {
            return data_get(self::RELATED, $status);
        }
        return ServiceStatus::ACCEPTED;
    }
}
