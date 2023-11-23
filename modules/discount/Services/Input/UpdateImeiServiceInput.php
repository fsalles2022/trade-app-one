<?php

declare(strict_types=1);

namespace Discount\Services\Input;

class UpdateImeiServiceInput implements Input
{
    /** @var string|null */
    private $authorization;

    /** @var string|null */
    private $authorizerCpf;

    /** @var string|null */
    private $serviceTransaction;

    /** @var string|null */
    private $newImei;

    /** @var string|null */
    private $customerCpf;

    /** @var string|null */
    private $oldImei;

    public function __construct(
        ?string $authorization,
        ?string $authorizerCpf,
        ?string $serviceTransaction,
        ?string $newImei,
        ?string $oldImei,
        ?string $customerCpf
    ) {
        $this->authorization      = $authorization;
        $this->authorizerCpf      = $authorizerCpf;
        $this->serviceTransaction = $serviceTransaction;
        $this->newImei            = $newImei;
        $this->oldImei            = $oldImei;
        $this->customerCpf        = $customerCpf;
    }

    public function getServiceTransaction(): ?string
    {
        return $this->serviceTransaction;
    }

    public function getCustomerCpf(): ?string
    {
        return $this->customerCpf;
    }

    public function getAuthorization(): ?string
    {
        return $this->authorization;
    }

    public function getAuthorizerCpf(): ?string
    {
        return $this->authorizerCpf;
    }

    public function getNewImei(): ?string
    {
        return $this->newImei;
    }

    public function getOldImei(): ?string
    {
        return $this->oldImei;
    }
}
