<?php

namespace TradeAppOne\Domain\Components\Helpers;

use TradeAppOne\Domain\Enumerators\ContextEnum;

class ContextHelper
{
    public static function getContext($permissions, string $subSystem, string $module) :string
    {
        if (is_null($permissions)) {
            return ContextEnum::CONTEXT_NON_EXISTENT;
        }

        if (self::notContains($subSystem, $permissions)) {
            return ContextEnum::CONTEXT_NON_EXISTENT;
        }

        if (self::notContains($module, $permissions[$subSystem])) {
            return ContextEnum::CONTEXT_NON_EXISTENT;
        }

        if (in_array(ContextEnum::CONTEXT_NETWORK, $permissions[$subSystem][$module])) {
            return ContextEnum::CONTEXT_NETWORK;
        }

        if (in_array(ContextEnum::CONTEXT_ALL, $permissions[$subSystem][$module])) {
            return ContextEnum::CONTEXT_ALL;
        }

        if (in_array(ContextEnum::CONTEXT_HIERARCHY, $permissions[$subSystem][$module])) {
            return ContextEnum::CONTEXT_HIERARCHY;
        }

        return ContextEnum::CONTEXT_NON_EXISTENT;
    }

    private static function notContains($key, $array)
    {
        return ! array_key_exists($key, $array);
    }
}
