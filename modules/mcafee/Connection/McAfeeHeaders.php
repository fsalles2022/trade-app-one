<?php

namespace McAfee\Connection;

class McAfeeHeaders
{
    public static function uri(): string
    {
        return config('integrations.mcafee.uri');
    }

    public static function getPartnerId(): string
    {
        return config('integrations.mcafee.partnerId');
    }
}
