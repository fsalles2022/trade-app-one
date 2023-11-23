<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class ServiceAlreadyInProgress extends BusinessRuleExceptions
{
    public function __construct(string $status = '')
    {
        $status        = trans('status.' . $status);
        $this->message = trans('messages.ServiceAlreadyInProgress', ['status' => $status]);
    }

    public function getShortMessage()
    {
        return 'ServiceAlreadyInProgress';
    }

    public function getDescription()
    {
        return;
    }

    public function getHttpStatus()
    {
        return Response::HTTP_NOT_ACCEPTABLE;
    }
}
