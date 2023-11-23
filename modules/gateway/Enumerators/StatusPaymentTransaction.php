<?php

declare(strict_types=1);

namespace Gateway\Enumerators;

final class StatusPaymentTransaction
{
    public const UNKNOWN_STATUS = 'UNKNOWN';
    public const STATUS_PAYMENT = [
        0 => 'WAITING FOR PAYMENT',
        1 => 'AUTHENTICATED',
        2 => 'UNAUTHORIZED',
        3 => 'AUTHORIZED',
        4 => 'UNAUTHORIZED',
        5 => 'IN CANCELLING',
        6 => 'CANCELLED',
        7 => 'IN CAPTURING',
        8 => 'AUTHORIZED',
        9 => 'UNAUTHORIZED',
        10 => 'RECURRING DONE',
        11 => 'BOLETO',
        12 => 'PARTIAL CANCELLED',
        56 => 'PARTIAL CANCELLED'
    ];

    public const STATUS_PAYMENT_TRANSLATE = [
        self::STATUS_PAYMENT[0]  => 'AGUARDANDO PAGAMENTO',
        self::STATUS_PAYMENT[1]  => 'AUTENTICADO',
        self::STATUS_PAYMENT[2]  => 'NÃO AUTENTICADO',
        self::STATUS_PAYMENT[3]  => 'AUTENTICADO',
        self::STATUS_PAYMENT[4]  => 'NÃO AUTENTICADO',
        self::STATUS_PAYMENT[5]  => 'EM CANCELAMENTO',
        self::STATUS_PAYMENT[6]  => 'CANCELADO',
        self::STATUS_PAYMENT[7]  => 'EM CAPTURA',
        self::STATUS_PAYMENT[8]  => 'AUTORIZADO',
        self::STATUS_PAYMENT[9]  => 'NÃO AUTORIZADO',
        self::STATUS_PAYMENT[10] => 'RECORRENTE FEITO',
        self::STATUS_PAYMENT[11] => 'BOLETO',
        self::STATUS_PAYMENT[12] => 'PARCIAL CANCELADO',
        self::STATUS_PAYMENT[56] => 'PARCIAL CANCELADO',
        self::UNKNOWN_STATUS     => 'DESCONHECIDO'
    ];

    public static function translate(string $statusInEnglish): string
    {
        return self::STATUS_PAYMENT_TRANSLATE[$statusInEnglish];
    }
}
