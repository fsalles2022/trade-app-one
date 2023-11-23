<?php

declare(strict_types=1);

namespace Discount\Services\Input;

class AuthorizationUpdateImeiInput
{
    /** @var string|null */
    private $login;

    /** @var string|null */
    private $password;

    /** @var string|null */
    private $serviceTransaction;

    public function __construct(?string $login, ?string $password, ?string $serviceTransaction)
    {
        $this->login              = $login;
        $this->password           = $password;
        $this->serviceTransaction = $serviceTransaction;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getServiceTransaction(): ?string
    {
        return $this->serviceTransaction;
    }
}
