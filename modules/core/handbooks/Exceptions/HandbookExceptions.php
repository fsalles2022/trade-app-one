<?php

namespace Core\HandBooks\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class HandbookExceptions
{
    public const NOT_FOUND           = 'handbookNotFound';
    public const OPERATION_NOT_FOUND = 'operationNotFound';
    public const TYPE_INVALID        = 'typeInvalid';

    public const HAS_NOT_PERMISSION_UNDER_HANDBOOK = 'userHasNotAuthorizationUnderHandbook';

    public static function notFound()
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_FOUND,
            'message' => trans('handbook::exceptions.' . self::NOT_FOUND),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }

    public static function operationNotFound()
    {
        return new BuildExceptions([
            'shortMessage' => self::OPERATION_NOT_FOUND,
            'message' => trans('handbook::exceptions.' . self::OPERATION_NOT_FOUND),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }

    public static function typeInvalid()
    {
        return new BuildExceptions([
            'shortMessage' => self::TYPE_INVALID,
            'message' => trans('handbook::exceptions.' . self::TYPE_INVALID),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }

    public static function hasNotAuthorizationUnderHandbook(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::HAS_NOT_PERMISSION_UNDER_HANDBOOK,
            'message' => trans('handbook::exceptions.' . self::HAS_NOT_PERMISSION_UNDER_HANDBOOK),
            'httpCode' => Response::HTTP_FORBIDDEN
        ]);
    }
}
