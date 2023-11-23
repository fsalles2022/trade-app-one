<?php

namespace VivoBR\Enumerators;

use TradeAppOne\Domain\Enumerators\Operations;

class VivoOperations
{
    const PRE                = Operations::VIVO_PRE;
    const CONTROLE           = Operations::VIVO_CONTROLE;
    const CONTROLE_GIGA_PASS = Operations::VIVO_CONTROLE;
    const CONTROLE_CARTAO    = Operations::VIVO_CONTROLE_CARTAO;
    const POS_FATURA         = Operations::VIVO_POS_PAGO;
    const INTERNET_PRE       = null;
    const INTERNET_POS       = Operations::VIVO_INTERNET_MOVEL_POS;
}
