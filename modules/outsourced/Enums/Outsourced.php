<?php

declare(strict_types=1);

namespace Outsourced\Enums;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

final class Outsourced
{
    public const PARTNER   = 'PARTNER_AUTHENTICATION';
    public const CLARO_V3  = 'CLARO_V3';
    public const TRADE_HUB = 'TRADE_HUB';

    public const RIACHUELO          = NetworkEnum::RIACHUELO;
    public const CEA                = NetworkEnum::CEA;
    public const VIA_VAREJO         = NetworkEnum::VIA_VAREJO;
    public const GPA                = NetworkEnum::GPA;
    public const EXTRA              = NetworkEnum::EXTRA;
    public const SURF_PERNAMBUCANAS = NetworkEnum::PERNAMBUCANAS;
    public const PERNAMBUCANAS      = NetworkEnum::PERNAMBUCANAS;
    public const MULTISOM           = NetworkEnum::MULTISOM;
    public const SCHUMANN           = NetworkEnum::SCHUMANN;
    public const CASAEVIDEO         = NetworkEnum::CASAEVIDEO;
}
