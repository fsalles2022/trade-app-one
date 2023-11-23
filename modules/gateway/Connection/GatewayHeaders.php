<?php

namespace Gateway\Connection;

class GatewayHeaders
{
    public static function getId()
    {
        return config('integrations.gateway.client-id');
    }

    public static function getAcessKey()
    {
        return config('integrations.gateway.client-secret');
    }
}
