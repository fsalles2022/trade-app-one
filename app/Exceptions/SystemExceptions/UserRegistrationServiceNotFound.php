<?php

namespace TradeAppOne\Exceptions\SystemExceptions;

class UserRegistrationServiceNotFound extends SystemException
{
    public function getShortMessage()
    {
        return 'UserRegistrationServiceNotFound';
    }
}
