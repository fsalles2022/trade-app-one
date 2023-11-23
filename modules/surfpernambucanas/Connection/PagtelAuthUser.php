<?php

declare(strict_types=1);

namespace SurfPernambucanas\Connection;

class PagtelAuthUser
{
    /** @var string */
    private $login;

    /** @var string */
    private $password;

    /** @var string */
    private $grantType;

    /** @var string */
    private $identify;

    /** @param mixed[] $configs */
    public function __construct(array $configs)
    {
        $this->login     = data_get($configs, 'login', '');
        $this->password  = data_get($configs, 'password', '');
        $this->grantType = data_get($configs, 'grant_type', '');
        $this->identify  = data_get($configs, 'identify', '');
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getGrantType(): string
    {
        return $this->grantType;
    }

    public function getIdentify(): string
    {
        return $this->identify;
    }
}
