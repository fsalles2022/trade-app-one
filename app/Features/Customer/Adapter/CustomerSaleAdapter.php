<?php

namespace TradeAppOne\Features\Customer\Adapter;

use TradeAppOne\Domain\Adapters\Adapter;
use TradeAppOne\Features\Customer\Customer;

class CustomerSaleAdapter implements Adapter
{
    private $customerData;

    public function __construct(?array $data)
    {
        $this->customerData = $data;
    }

    public function adapt(): array
    {
        $customerKeys = (new Customer())->getFillable();

        $customer = [];
        foreach ($customerKeys as $key) {
            $customer[$key] = data_get($this->customerData, "0.customer.{$key}");
        }

        return $customer;
    }
}
