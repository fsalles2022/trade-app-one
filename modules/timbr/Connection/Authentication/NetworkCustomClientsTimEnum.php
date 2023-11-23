<?php

declare(strict_types=1);

namespace TimBR\Connection\Authentication;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

final class NetworkCustomClientsTimEnum
{
    public const CUSTOM_CLIENTS = [
        NetworkEnum::VIA_VAREJO,
        NetworkEnum::AVENIDA,
        NetworkEnum::MULTISOM,
        NetworkEnum::COLOMBO,
        NetworkEnum::LOJAS_TORRA,
        NetworkEnum::LE_BISCUIT,
        NetworkEnum::MERCADO_MOVEIS,
        NetworkEnum::SAMSUNG,
        NetworkEnum::SAMSUNG_MRF,
        NetworkEnum::MASTERCELL,
        NetworkEnum::IPLACE,
    ];
}
