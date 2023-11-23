<?php

namespace ClaroBR\Enumerators;

use ClaroBR\Exceptions\AttributeNotFound;
use TradeAppOne\Domain\Enumerators\Modes;

final class ClaroBRDependents
{
    const CLARO_DADOS     = 'CLARO_DADOS';
    const CLARO_VOZ_DADOS = 'CLARO_VOZ_DADOS';
    const CLARO_CONTROLE  = 'CLARO_CONTROLE';

    const VOZ_DADOS = 'VOZ_DADOS';
    const DADOS     = 'DADOS';
    const CONTROLE  = 'CONTROLE';

    protected static $typeTranslations = [
        self::VOZ_DADOS => self::CLARO_VOZ_DADOS,
        self::DADOS     => self::CLARO_DADOS,
        self::CONTROLE  => self::CLARO_CONTROLE,
    ];

    protected static $modeTranslations = [
        'NOVO'    => Modes::ACTIVATION,
        'BASE'    => Modes::MIGRATION,
        'PORTADO' => Modes::PORTABILITY,
    ];

    public static function translateMode($mode): string
    {
        if ($mode) {
            $translated = data_get(self::$modeTranslations, $mode, '');
            if ($translated) {
                return $translated;
            }
        }
        throw new \InvalidArgumentException();
    }

    public static function translateType($type): string
    {
        if ($type) {
            $translated = data_get(self::$typeTranslations, $type, '');
            if ($translated) {
                return $translated;
            }
        }

        throw new \InvalidArgumentException();
    }

    public static function translateModeToRequest($mode): string
    {
        if ($mode) {
            $flippedTranslations = array_flip(self::$modeTranslations);
            $translated          = data_get($flippedTranslations, $mode, '');
            if ($translated) {
                return $translated;
            }
        }
        throw new AttributeNotFound($mode);
    }

    public static function translateTypeToRequest($type): string
    {
        if ($type) {
            $flippedTranslations = array_flip(self::$typeTranslations);
            $translated          = data_get($flippedTranslations, $type, '');
            if ($translated) {
                return $translated;
            }
        }
        throw new AttributeNotFound($type);
    }
}
