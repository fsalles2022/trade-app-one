<?php

namespace TradeAppOne\Domain\Enumerators;

/**
 * @deprecated
 * @deprecated No longer used by internal code and not recommended.
 * @deprecated Use each respective permission.
 */
final class Permissions
{
    public const PAINEL_VIEW                   = 'PAINEL.VIEW';
    public const DASHBOARD_VIEW                = 'DASHBOARD.VIEW';
    public const SALE_CREATE                   = 'SALE.CREATE';
    public const SALE_FLOW                     = 'SALE.FLOW';
    public const SALE_VIEW_EVALUATIONS         = 'SALE.VIEW_EVALUATIONS';
    public const DASHBOARD_TRADEIN             = 'DASHBOARD_TRADEIN.VIEW';
    public const DASHBOARD_MCAFEE              = 'DASHBOARD_MCAFEE.VIEW';
    public const DASHBOARD_MCAFEE_ALL          = 'DASHBOARD_MCAFEE.ALL';
    public const DASHBOARD_CEA_GOALS           = 'DASHBOARD_CEA_GOALS.VIEW';
    public const DASHBOARD_RIACHUELO_GOALS     = 'DASHBOARD_RIACHUELO_GOALS.VIEW';
    public const DASHBOARD_PERNAMBUCANAS_SALES = 'DASHBOARD_PERNAMBUCANAS_SALES.VIEW';
    public const DASHBOARD_COMMISSION_TIM      = 'DASHBOARD_COMMISSION_TIM.VIEW';
    public const DASHBOARD_CLARO_MARKET_SHARE  = 'DASHBOARD_CLARO_MARKET_SHARE.VIEW';
}
