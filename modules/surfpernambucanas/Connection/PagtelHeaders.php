<?php

declare(strict_types=1);

namespace SurfPernambucanas\Connection;

class PagtelHeaders
{
    /** @var string */
    private $uri;

    /** @var mixed[] */
    private $headers;

    /** @var float */
    private $timeoutConnection;

    private const DEFAULT_TIMEOUT_CONNECTION = 120.0;

    /** @param mixed[] $configs */
    public function __construct(array $configs)
    {
        $this->uri               = data_get($configs, 'uri', '');
        $this->headers           = data_get($configs, 'headers', []);
        $this->timeoutConnection = self::DEFAULT_TIMEOUT_CONNECTION;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    /** @return array[] */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getTimeoutConnection(): float
    {
        return $this->timeoutConnection;
    }
}
