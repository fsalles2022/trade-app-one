<?php

declare(strict_types=1);

namespace ClaroBR\Exceptions;

use Illuminate\Http\Response;

use TradeAppOne\Exceptions\BuildExceptions;

class UserAuthAlternateExceptions
{
    public const DOCUMENT_ALREADY_EXISTS = 'documentAlreadyExistsInNetwork';

    public static function documentAlreadyExists(?string $message = null): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::DOCUMENT_ALREADY_EXISTS,
            'message' => trans('Matricula ja existente nessa rede'),
            'httpCode' => Response::HTTP_PRECONDITION_FAILED,
            'description' => $message
        ]);
    }
}
