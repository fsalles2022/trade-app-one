<?php

declare(strict_types=1);

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class ProtocolNoExists extends BusinessRuleExceptions
{

    public function getShortMessage()
    {
        return 'ProtocolOfSaleNoExists';
    }

    public function getDescription()
    {
        return trans('exceptions.protocol_no_exists');
    }

    public function getHelp()
    {
        return trans('help.protocol_no_exists');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
