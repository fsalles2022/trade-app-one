<?php

declare(strict_types=1);

namespace TimBR\Enumerators;

class TimBRServices
{
    private const CONTROLE_DEEZER_OFFER = '1-1EH21XO';
    private const EXPRESS_DEEZER_OFFER  = '1-1EH21Y7';

    public const DEEZER_TYPE = 'DEEZER';
    public const PLUGIN_TYPE = 'PLUGIN';

    public const DEEZER_SERVICES = [
        self::CONTROLE_DEEZER_OFFER,
        self::EXPRESS_DEEZER_OFFER,
    ];

    public const AVAILABLE_SERVICES = [
        self::CONTROLE_DEEZER_OFFER,
        self::EXPRESS_DEEZER_OFFER,
    ];
}
