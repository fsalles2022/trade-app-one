<?php


namespace GA\Connections\Headers;

class GAHeaders
{
    public const ACTIVATIONS = 'activations';

    public static function getUri()
    {
        return config(self::ACTIVATIONS.'.uri');
    }

    public static function getClient()
    {
        return config(self::ACTIVATIONS.'.client');
    }

    public static function getApiKey()
    {
        return config(self::ACTIVATIONS.'.x_api_key');
    }
}
