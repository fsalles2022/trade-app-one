<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class SalePermission extends BasePermission
{
    public const NAME = 'SALE';

    public const ACTIVATE                           = PermissionActions::ACTIVATE;
    public const CREATE                             = PermissionActions::CREATE;
    public const EDIT                               = PermissionActions::EDIT;
    public const EDIT_STATUS                        = PermissionActions::EDIT_STATUS;
    public const FLOW                               = PermissionActions::FLOW;
    public const LOG                                = PermissionActions::LOG;
    public const VIEW                               = PermissionActions::VIEW;
    public const CANCEL                             = PermissionActions::CANCEL;
    public const VIEW_ONLY_TRADE_IN                 = PermissionActions::VIEW_ONLY_TRADE_IN;
    public const PRINT                              = 'PRINT';
    public const CREATE_BACKOFFICE                  = 'CREATE_BACKOFFICE';
    public const ASSOCIATE                          = 'ASSOCIATE';
    public const VIEW_BACKOFFICE                    = 'VIEW_BACKOFFICE';
    public const OI_RESIDENTIAL                     = 'OI_RESIDENTIAL_SALE';
    public const AUTOMATIC_REGISTRATION             = 'AUTOMATIC_REGISTRATION';
    public const CONTROLE_FACIL_V3                  = 'CONTROLE_FACIL_V3';
    public const CONTROLE_FACIL_V3_ACTIVATION       = 'CONTROLE_FACIL_V3_ACTIVATION';
    public const CONTROLE_FACIL_V3_MIGRATION        = 'CONTROLE_FACIL_V3_MIGRATION';
    public const CONTROLE_FACIL_V3_PORTABILITY      = 'CONTROLE_FACIL_V3_PORTABILITY';
    public const TRADE_HUB_CLARO_PRE_PAGO           = 'TRADE_HUB_CLARO_PRE_PAGO';
    public const TRADE_HUB_CLARO_CONTROLE_FACIL     = 'TRADE_HUB_CLARO_CONTROLE_FACIL';
    public const TRADE_HUB_CLARO_CONTROLE_BOLETO    = 'TRADE_HUB_CLARO_CONTROLE_BOLETO';
    public const TRADE_HUB_CLARO_RESIDENTIAL        = 'TRADE_HUB_CLARO_RESIDENTIAL';
    public const TRADE_HUB_TIM_PRE_PAGO             = 'TRADE_HUB_TIM_PRE_PAGO';
    public const TRADE_HUB_TIM_CONTROLE_EXPRESS     = 'TRADE_HUB_TIM_CONTROLE_EXPRESS';
    public const TRADE_HUB_BACK_OFFICE_RCV          = 'TRADE_HUB_BACK_OFFICE_RCV';
    public const TRADE_HUB_BACK_OFFICE_SALE_MANAGER = 'TRADE_HUB_BACK_OFFICE_SALE_MANAGER';
    public const TRADE_HUB_SALE_LIST                = 'TRADE_HUB_SALE_LIST';
    public const TRADE_HUB_SALE_ADMINISTRATOR       = 'TRADE_HUB_SALE_ADMINISTRATOR';
}
