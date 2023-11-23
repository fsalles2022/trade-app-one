<?php

namespace VivoBR\Connection\Headers;

interface SunHeader
{
    public function getUri(): string;

    public function getHeaders(): array;

    public function getToken(): string;
}
