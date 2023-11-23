<?php

namespace NextelBR\Connection\NextelBR\Headers;

class NextelBRHeaders implements NextelBRHeadersInterface
{
    public static function uri(): string
    {
        return config('integrations.nextel.uri');
    }

    public static function headers(): array
    {
        return ['Content-Type' => 'application/json', 'x-api-key' => self::xApiKey()];
    }

    public static function xApiKey(): string
    {
        return config('integrations.nextel.api-key');
    }

    public function getChannel(): string
    {
        return config('integrations.nextel.channel');
    }
}
