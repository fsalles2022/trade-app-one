<?php

namespace TradeAppOne\Domain\Enumerators;

final class ScopeEnum
{
    const EVERYTHING             = 0;
    const ALL_SYSTEM             = 100;
    const _                      = 200;
    const ALL_NETWORKS           = 300;
    const GROUP_OF_NETWORKS      = 400;
    const OWN_NETWORK            = 500;
    const GROUP_OF_HIERARCHY     = 600;
    const OWN_HIERARCHY          = 700;
    const GROUP_OF_POINT_OF_SALE = 800;
    const OWN_POINT_OF_SALE      = 900;
    const OWN                    = 1000;
}
