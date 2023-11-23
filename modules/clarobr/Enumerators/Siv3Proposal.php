<?php
declare(strict_types=1);

namespace ClaroBR\Enumerators;

use TradeAppOne\Domain\Enumerators\Operations;

final class Siv3Proposal
{
    public const ORIGIN_CONTROLE_BOLETO = 'TRADE_APP_ONE_CB';
    public const ORIGIN_CONTROLE_FACIL  = 'TRADE_APP_ONE_CF';
    
    public const AVAILABLE_ORIGINS = [
        Operations::CLARO_CONTROLE_BOLETO => self::ORIGIN_CONTROLE_BOLETO,
        Operations::CLARO_CONTROLE_FACIL => self::ORIGIN_CONTROLE_FACIL,
    ];

    public static function getEnumByOperation(?string $operation): ?string
    {
        return self::AVAILABLE_ORIGINS[$operation] ?? null;
    }
}
