<?php

namespace TradeAppOne\Tests\Helpers;

use TradeAppOne\Notifications\NotifyUser;

trait EmailHelper
{
    public function mockEmail()
    {
        app()->bind(NotifyUser::class, function () {
            $notify = $this->getMockBuilder(NotifyUser::class)
                ->setMethods(['sendNotification'])
                ->getMock();
            
            $notify->method('sendNotification')->will($this->returnValue(null));
            return $notify;
        });
    }
}
