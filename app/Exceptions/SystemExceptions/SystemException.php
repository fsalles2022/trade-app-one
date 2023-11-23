<?php

namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\CustomRuleExceptions;

/**
 * @deprecated Start following RoleExceptions way to create exceptions.
 */
abstract class SystemException extends CustomRuleExceptions
{
    public function getHttpStatus()
    {
        return Response::HTTP_CONFLICT;
    }

    public function getDescription()
    {
        return 'SystemException';
    }
}
