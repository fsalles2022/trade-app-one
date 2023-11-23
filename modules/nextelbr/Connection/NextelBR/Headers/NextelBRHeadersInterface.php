<?php

namespace NextelBR\Connection\NextelBR\Headers;

interface NextelBRHeadersInterface
{
    const PREFIX = 'nextel-controle/varejo/v1';

    public static function uri(): string;

    public static function headers(): array;

    public static function xApiKey(): string;

    public function getChannel(): string;
}
