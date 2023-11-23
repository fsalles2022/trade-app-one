<?php

namespace TradeAppOne\Features\Customer\Adapter;

use TradeAppOne\Domain\Adapters\Adapter;
use TradeAppOne\Features\Customer\Customer;

class CustomerNotNested implements Adapter
{
    private $customerData;

    public function __construct(array $array)
    {
        $this->customerData = $array;
    }

    public function adapt()
    {
        $customerKeys = (new Customer())->getFillable();

        $customer = [];
        foreach ($customerKeys as $key) {
            $customer[$key] = data_get($this->customerData, "{$key}");
        }

        return $customer;
    }
}
