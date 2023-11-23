<?php

namespace TimBR\Connection\Headers;

interface TimHeader
{
    public function credentials(): array;

    public function getClientId(): string;

    public function getClientSecret(): string;

    public function getBasicAuth(): string;

    public function getRedirectUri(): string;

    public function getRedirectUriEncoded(): string;

    public function getSergeant(string $cpf): string;
}
