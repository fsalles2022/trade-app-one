<?php

namespace Gateway\tests\Helpers;

use Carbon\Carbon;

trait GatewayFactoriesHelper
{
    public function payloadCreditCard()
    {
        return [
            'flag' => 'visa',
            'cvv' => '123',
            'month' => Carbon::now()->format('m'),
            'name' => 'João das Neves',
            'pan' => '4242424242424242',
            'year' => Carbon::now()->format('y'),
            'times' => 1
        ];
    }
}
