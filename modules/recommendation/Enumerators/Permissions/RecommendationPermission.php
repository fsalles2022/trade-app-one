<?php


namespace Recommendation\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\Permissions\BasePermission;

final class RecommendationPermission extends BasePermission
{
    public const NAME = 'RECOMMENDATION';

    public const SALE_INDICATION = 'SALE.INDICATION';
}
