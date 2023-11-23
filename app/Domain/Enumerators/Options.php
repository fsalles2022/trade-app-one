<?php


namespace TradeAppOne\Domain\Enumerators;

use TradeAppOne\Exceptions\SystemExceptions\ServiceOptionsExceptions;

final class Options
{
    public const AUTENTICA_PROMOTOR = 'AUTENTICA_PROMOTOR';
    public const AUTENTICA_VENDEDOR = 'AUTENTICA_VENDEDOR';

    public const AUTENTCA_OPERATIONS = [
        Operations::CLARO_POS,
        Operations::CLARO_CONTROLE_BOLETO,
        Operations::CLARO_BANDA_LARGA
    ];

    public const AUTENTICA_RELATION = [
        self::AUTENTICA_PROMOTOR => self::AUTENTCA_OPERATIONS,
        self::AUTENTICA_VENDEDOR => self::AUTENTCA_OPERATIONS,
    ];

    public static function getOperationsByOptions(string $option, array $operations): array
    {
        throw_unless(isset($operations[$option]), ServiceOptionsExceptions::serviceOptionsOperationsNotAvailable());

        return $operations[$option];
    }
}
