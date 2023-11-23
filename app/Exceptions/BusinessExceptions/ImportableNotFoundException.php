<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class ImportableNotFoundException extends BusinessRuleExceptions
{

    public function getShortMessage()
    {
        return 'ImportableNotFound';
    }

    public function getDescription()
    {
        return trans('exceptions.importable.not_found');
    }

    public function getHttpStatus()
    {
        return Response::HTTP_NOT_FOUND;
    }
}
