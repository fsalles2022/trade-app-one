<?php

namespace Movile;

class Movile
{
    public static function origin()
    {
        return 'trade_up_group';
    }

    public static function applicationId()
    {
        return config('integrations.movile.application-id');
    }

    public static function uri()
    {
        return config('integrations.movile.uri');
    }
}
