<?php

namespace Buyback\Enumerators;

use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Domain\Enumerators\Permissions\BasePermission;

class QuizPermissions extends BasePermission
{
    public const NAME = 'QUIZ';

    public const VIEW   = PermissionActions::VIEW;
    public const CREATE = PermissionActions::CREATE;
    public const EDIT   = PermissionActions::EDIT;
}
