<?php

namespace Buyback\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BusinessExceptions\BusinessRuleExceptions;

class DeviceNotFoundException extends BusinessRuleExceptions
{
    public function __construct(string $description = '')
    {
        $this->description = $description;
        $this->message     = trans('buyback::exceptions.device_not_found.message');
    }

    public function getShortMessage()
    {
        return 'DeviceNotFoundException';
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getHttpStatus()
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
