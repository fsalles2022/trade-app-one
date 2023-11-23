<?php

namespace Banner\Exceptions;

use Illuminate\Http\Response;

class ErrorSavingImages extends ApiException
{
    public function __construct(string $transportedMessage = "")
    {
        $this->message            = '';
        $this->transportedMessage = $transportedMessage;
    }

    public function getHttpStatus()
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
