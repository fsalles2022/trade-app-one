<?php

namespace ClaroBR\Enumerators;

use TradeAppOne\Domain\Enumerators\Operations;

class ClaroOperations
{
    const POS_PAGO         = Operations::CLARO_POS;
    const CONTROLE_BOLETO  = Operations::CLARO_CONTROLE_BOLETO;
    const CONTROLE_FACIL   = Operations::CLARO_CONTROLE_FACIL;
    const PRE_PAGO         = Operations::CLARO_PRE;
    const PRE_PAGO_EXTERNA = Operations::CLARO_PRE_EXTERNAL_SALE;
    const BANDA_LARGA      = Operations::CLARO_BANDA_LARGA;
}
