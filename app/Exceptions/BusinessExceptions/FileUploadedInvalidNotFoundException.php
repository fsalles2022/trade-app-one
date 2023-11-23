<?php

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;

class FileUploadedInvalidNotFoundException extends BusinessRuleExceptions
{
    public function __construct(string $description = '')
    {
        $this->description = $description;
        $this->message     = trans('exceptions.importable.file_uploaded_invalid');
    }

    public function getShortMessage()
    {
        return 'FileUploadedInvalidNotFoundException';
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
