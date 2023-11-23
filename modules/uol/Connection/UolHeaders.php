<?php

namespace Uol\Connection;

class UolHeaders
{
    public static function uri(): string
    {
        return config('integrations.uol.uri');
    }

    public static function getMail(): string
    {
        return config('integrations.uol.mail');
    }

    public static function getPassword(): string
    {
        return config('integrations.uol.password');
    }
}
