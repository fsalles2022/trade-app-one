<?php

namespace TradeAppOne\Tests\Helpers;

trait MobileSecurityHelper
{
    public $mobileSecurityCustomer = [
        'firstName' => 'First',
        'lastName' => 'Last',
        'email' => 'first@last.com'
    ];

    public function getMobileSecurityFilled()
    {
        return [
            'operator' => 'MCAFEE',
            'operation' => 'MOBILE_SECURITY',
            'password' => '2134676',
            'customer' => $this->mobileSecurityCustomer
        ];
    }
}
