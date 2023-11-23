<?php

declare(strict_types=1);

namespace Discount\Services\Output;

class GetSaleWithImeiOutput implements Output
{
    /** @var string|null */
    private $customerFirstName;

    /** @var string|null */
    private $customerLastName;

    /** @var string|null */
    private $serviceTransaction;

    /** @var string|null */
    private $customerCpf;

    /** @var string|null */
    private $imei;

    /** @var string|null */
    private $buyDate;

    public function __construct(
        ?string $serviceTransaction,
        ?string $customerCpf,
        ?string $imei,
        ?string $customerFirstName,
        ?string $customerLastName,
        ?string $buyDate
    ) {
        $this->serviceTransaction = $serviceTransaction;
        $this->customerCpf        = $customerCpf;
        $this->customerFirstName  = $customerFirstName;
        $this->customerLastName   = $customerLastName;
        $this->imei               = $imei;
        $this->buyDate            = $buyDate;
    }

    public function getCustomerCpf(): ?string
    {
        return $this->customerCpf;
    }

    public function getCustomerFirstName(): ?string
    {
        return $this->customerFirstName;
    }

    public function getCustomerLastName(): ?string
    {
        return $this->customerLastName;
    }

    public function getImei(): ?string
    {
        return $this->imei;
    }

    public function getServiceTransaction(): ?string
    {
        return $this->serviceTransaction;
    }

    public function getBuyDate(): ?string
    {
        return $this->buyDate;
    }

    /** @return mixed[] */
    public function jsonSerialize(): array
    {
        return [
            'serviceTransaction' => $this->getServiceTransaction(),
            'customer' => [
                'cpf' => $this->getCustomerCpf(),
                'name' => $this->getCustomerFirstName() . ' ' . $this->getCustomerLastName(),
            ],
            'imei' => $this->getImei(),
            'buyDate' => $this->getBuyDate()
        ];
    }
}
