<?php

namespace Movile\Connection\Headers;

class MovileHeader
{
    public static function getHeaders(string $body): array
    {
        return [
            'x-kiwi-credential-id' => config('integrations.movile.credential'),
            'x-kiwi-signature'     => MovileSignature::generate($body),
            'Content-Type'         => 'application/json',
            'Accept'               => 'application/json',
        ];
    }
}
