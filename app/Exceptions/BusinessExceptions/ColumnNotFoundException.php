<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class ColumnNotFoundException extends BusinessRuleExceptions
{
    public function __construct(string $column)
    {
        $this->message = trans('exceptions.importable.column_not_found', ['column' => $column]);
    }
    
    public function getShortMessage()
    {
        return 'ColumnNotFoundException';
    }

    public function getDescription()
    {
        return $this->message;
    }

    public function getHelp()
    {
        return trans('help.importable.column_not_found');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
