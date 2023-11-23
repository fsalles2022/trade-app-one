<?php


namespace TradeAppOne\Domain\Enumerators;

final class Channels
{
    public const VAREJO        = 'VAREJO';
    public const MASTER_DEALER = 'MASTER_DEALER';
    public const DISTRIBUICAO  = 'DISTRIBUIÇÃO';

    public const AVAILABLE = [
        self::VAREJO,
        self::MASTER_DEALER,
        self::DISTRIBUICAO
    ];
}
