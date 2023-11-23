<?php

namespace TradeAppOne\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class NoDatabase extends CustomRuleExceptions
{
    public function __construct(string $message = "")
    {
        $this->message = trans('exceptions.no_database.message');
    }

    public function getDescription()
    {
        return trans('exceptions.no_database.message');
    }

    public function getHelp()
    {
        return trans('help.no_database');
    }

    public function getShortMessage()
    {
        return 'NoDatabase';
    }

    public function getTransportedMessage()
    {
        return '';
    }

    public function render()
    {
        return response(['errors' => [$this->getError()]], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function report()
    {
        Log::EMERGENCY('DATABASE NOT FOUND');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
