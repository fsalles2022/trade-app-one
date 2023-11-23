<?php

namespace ClaroBR\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\ThirdPartyExceptions;

class PointOfSaleWithoutSivException extends ThirdPartyExceptions
{
    public function __construct(string $message = "")
    {
        $this->transportedMessage = $message;
    }

    public function getDescription()
    {
        return trans('exceptions.point_of_sale_without_siv');
    }

    public function getShortMessage()
    {
        return 'PointOfSaleWithoutSiv';
    }

    public function getHelp()
    {
        return 'exceptions.point_of_sale_without_siv';
    }

    public function getTransportedMessage()
    {
        return $this->transportedMessage;
    }

    public function getHttpStatus()
    {
        return Response::HTTP_MISDIRECTED_REQUEST;
    }
}
