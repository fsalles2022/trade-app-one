<?php

namespace TradeAppOne\Domain\Enumerators;

final class PermissionActions
{
    const CREATE = "CREATE";
    const VIEW   = "VIEW";
    const EDIT   = "EDIT";
    const CANCEL = "CANCEL";
    const ALL    = "ALL";
    const USE    = "USE";

    /**
     * @deprecated
     */
    const VIEW_ONLY_TRADE_IN = "VIEW_ONLY_TRADE_IN";
    const DELETE             = "DELETE";

    const APPROVE = "APPROVE";
    const REJECT  = "REJECT";

    const PRINT              = "PRINT";
    const IMPORT             = "IMPORT";
    const EXPORT             = "EXPORT";
    const PERSONIFY          = "PERSONIFY";
    const LOG                = "LOG";
    const FLOW               = "FLOW";
    const UPDATE_PREFERENCES = "UPDATE_PREFERENCES";
    const EDIT_STATUS        = "EDIT_STATUS";
    const ACTIVATE           = "ACTIVATE";
}
