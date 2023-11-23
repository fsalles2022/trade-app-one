<?php

namespace Banner\Exceptions;

use Illuminate\Http\Response;

class ModelInvalidException extends ApiException
{
    public function __construct(string $message = "")
    {
        $this->message = $message;
    }

    public function getShortMessage()
    {
        return 'ModelInvalidException';
    }

    public function getHttpStatus()
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
