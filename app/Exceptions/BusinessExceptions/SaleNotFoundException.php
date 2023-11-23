<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class SaleNotFoundException extends BusinessRuleExceptions
{
    public function __construct(string $message = "")
    {
        $this->transportedMessage = $message;
    }

    public function getDescription()
    {
        return trans('exception.sale_no_exists');
    }

    public function getHelp()
    {
        return trans('help.sale_no_exists');
    }

    public function getShortMessage()
    {
        return 'SaleNotFound';
    }

    public function getTransportedMessage()
    {
        return '';
    }

    public function getHttpStatus()
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
