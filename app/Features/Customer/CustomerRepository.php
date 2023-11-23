<?php

namespace TradeAppOne\Features\Customer;

use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class CustomerRepository extends BaseRepository
{
    protected $model = Customer::class;
}
