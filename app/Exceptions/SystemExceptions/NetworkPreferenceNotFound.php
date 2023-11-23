<?php

namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\CustomRuleExceptions;

class NetworkPreferenceNotFound extends CustomRuleExceptions
{
    public function getShortMessage()
    {
        return 'NetworkPreferenceNotFound';
    }

    public function getDescription()
    {
        return 'NetworkPreferenceNotFound';
    }

    public function getHttpStatus()
    {
        return Response::HTTP_NOT_FOUND;
    }
}
