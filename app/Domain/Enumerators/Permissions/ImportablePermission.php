<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

final class ImportablePermission extends BasePermission
{
    public const NAME = "IMPORTABLE";

    public const USER                      = UserPermission::NAME;
    public const POINT_OF_SALE             = PointOfSalePermission::NAME;
    public const USERS_DELETE              = UserPermission::NAME . '_' . UserPermission::DELETE;
    public const OI_RESIDENTIAL_SALE       = SalePermission::OI_RESIDENTIAL;
    public const AUTOMATIC_REGISTRATION    = SalePermission::AUTOMATIC_REGISTRATION;
    public const USER_PASSWORD_MASS_UPDATE = UserPermission::NAME . '_' . UserPermission::PASSWORD_MASS_UPDATE;
    public const TTM_REBATE                = TimRebatePermission::NAME;
}
