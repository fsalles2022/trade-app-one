<?php

namespace NextelBR\Connection\M4uModal\Headers;

class NextelBRTradeUpHeader
{
    public static function uri(): string
    {
        return config('integrations.nextel.modal.uri');
    }

    public static function headers(): array
    {
        return [
            'Authorization' => 'apiKey ' . NextelBRTradeUpHeader::apiKey(),
            'Content-Type'  => 'application/json'
        ];
    }

    public static function apiKey(): string
    {
        return config('integrations.nextel.modal.api-key');
    }
}
