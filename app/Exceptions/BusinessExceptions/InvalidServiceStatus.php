<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class InvalidServiceStatus extends BusinessRuleExceptions
{
    public function __construct(string $status = "")
    {
        $this->message = trans('exceptions.invalid_service_status', ['status' => $status]);
    }

    public function getShortMessage()
    {
        return 'InvalidServiceStatus';
    }

    public function getDescription()
    {
        return $this->message;
    }

    public function getHttpStatus()
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
