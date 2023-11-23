<?php

declare(strict_types=1);

namespace ClaroBR;

class Siv3Headers
{
    /** @var string */
    private $uri;

    /** @var string[] */
    private $headers;

    /** @var string */
    private $login;

    /** @var string */
    private $password;

    public function __construct(array $configs)
    {
        $this->uri      = data_get($configs, 'uri');
        $this->login    = data_get($configs, 'login', '');
        $this->password = data_get($configs, 'password', '');
        $this->headers  = data_get($configs, 'headers', []);
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    /** @return string[] */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /** @return string[] */
    public function getCredentials(): array
    {
        return [
            'email' => $this->login,
            'password' => $this->password
        ];
    }
}
