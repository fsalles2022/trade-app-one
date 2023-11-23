<?php

namespace VivoTradeUp\Repositories;

class VivoPre
{
    private static $plan = [
        [
            'id' => 1,
            'nome' => 'PRÉ PAGO',
            'ddd' => 11,
            'valor' => 0,
            'tipo' => 'PRE',
        ]
    ];

    public static function getVivoPre(): array
    {
        return self::$plan;
    }
}
