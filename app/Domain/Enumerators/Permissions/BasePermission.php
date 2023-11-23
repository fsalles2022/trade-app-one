<?php

namespace TradeAppOne\Domain\Enumerators\Permissions;

use ReflectionClass;
use TradeAppOne\Domain\Enumerators\ContextEnum;

class BasePermission
{
    const NAME        = 'NAME';
    const ACTION      = 'ACTION';
    const DESCRIPTION = 'DESCRIPTION';

    const CONTEXT_ALL          = ContextEnum::CONTEXT_ALL;
    const CONTEXT_HIERARCHY    = ContextEnum::CONTEXT_HIERARCHY;
    const CONTEXT_OWN          = ContextEnum::CONTEXT_OWN;
    const CONTEXT_NON_EXISTENT = ContextEnum::CONTEXT_NON_EXISTENT;
    const CONTEXT_NETWORK      = ContextEnum::CONTEXT_NETWORK;


    public static function getConstants(): array
    {
        try {
            $calledClass    = new ReflectionClass(get_called_class());
            $currentClass   = new ReflectionClass(__CLASS__);
            $permissionName = $calledClass->getConstant(self::NAME);
            $allPermissions = array_except($calledClass->getConstants(), $currentClass->getConstants());
            foreach ($allPermissions as $index => $permission) {
                $allPermissions[$index] = $permissionName . "." . $permission;
            }
            return $allPermissions;
        } catch (\ReflectionException $e) {
            return [];
        }
    }

    public static function getFullName($permission): string
    {
        $calledClass    = new ReflectionClass(get_called_class());
        $permissionName = $calledClass->getConstant(self::NAME);
        return $permissionName . "." . $permission;
    }
}
