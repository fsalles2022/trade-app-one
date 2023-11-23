<?php

namespace Outsourced\Cea\tests\ServerTest\GiftCard;

use stdClass;

class CeaResponseMock extends \stdClass
{
    public const ID_TRANSACAO = 36446116;

    public static function activate(): stdClass
    {
        $response               = new \stdClass();
        $response->NumeroCartao = '10010907063026970';
        $response->Status       = 'ATIVO';
        $response->ValorSaldo   = "50.00";
        $response->IDTransacao  = self::ID_TRANSACAO;

        return $response;
    }
}
