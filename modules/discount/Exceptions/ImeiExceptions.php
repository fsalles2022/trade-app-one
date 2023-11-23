<?php

declare(strict_types=1);

namespace Discount\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class ImeiExceptions
{
    public const UNAUTHORIZED = 'unauthorized';

    public static function unauthorized(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UNAUTHORIZED,
            'message' => trans('discount::exceptions.' . self::UNAUTHORIZED),
            'httpCode' => Response::HTTP_FORBIDDEN
        ]);
    }
}
