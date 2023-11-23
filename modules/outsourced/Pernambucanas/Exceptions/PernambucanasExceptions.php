<?php

declare(strict_types=1);

namespace Outsourced\Pernambucanas\Exceptions;

use Illuminate\Http\Response;
use Outsourced\Enums\Outsourced;
use TradeAppOne\Exceptions\BuildExceptions;

class PernambucanasExceptions
{
    public const UNAVAILABLE = 'pernambucanasUnavailable';

    public static function unavailable(?string $message): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UNAVAILABLE,
            'message' => trans(Outsourced::PERNAMBUCANAS . '::exceptions.' . self::UNAVAILABLE),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'description' => $message
        ]);
    }
}
