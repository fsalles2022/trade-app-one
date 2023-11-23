<?php

namespace TradeAppOne\Tests\Helpers;

use TradeAppOne\Domain\Enumerators\Operations;

trait RouboFurtoHelper
{
    public $rouboFurtoCustomer = [
        'firstName' => 'First',
        'lastName'  => 'Last',
        'email'     => 'first@last.com'
    ];

    public function getRouboFurtoFilled()
    {
        return [
            'sector'    => Operations::INSURERS,
            'operator'  => 'MAPFRE',
            'operation' => 'ROUBO_FURTO',
            'customer'  => $this->rouboFurtoCustomer,
            'status'    => \TradeAppOne\Domain\Enumerators\ServiceStatus::SUBMITTED
        ];
    }
}
