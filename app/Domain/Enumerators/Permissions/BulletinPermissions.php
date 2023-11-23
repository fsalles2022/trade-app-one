<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

use TradeAppOne\Domain\Enumerators\PermissionActions;

final class BulletinPermissions
{
    public const NAME = 'BULLETIN';

    public const CREATE = PermissionActions::CREATE;
    public const EDIT   = PermissionActions::EDIT;
    public const VIEW   = PermissionActions::VIEW;

    public const DESCRIPTIONS = [
        self::NAME . '.' . self::VIEW => 'Exibir Painel de Comunicados',
        self::NAME . '.' . self::CREATE => 'Criar Painel de Comunicados',
        self::NAME . '.' . self::EDIT => 'Editar Painel de Comunicados',
    ];
}
