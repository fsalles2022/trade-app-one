<?php

declare(strict_types=1);

namespace Discount\Services\Input;

class GetSaleWithImeiInput
{
    /** @var string */
    private $cpf;

    /** @var string */
    private $serviceTransaction;

    public function __construct(?string $cpf, ?string $serviceTransaction)
    {
        $this->cpf                = $cpf;
        $this->serviceTransaction = $serviceTransaction;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function getServiceTransaction(): ?string
    {
        return $this->serviceTransaction;
    }
}
