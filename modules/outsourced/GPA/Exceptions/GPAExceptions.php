<?php


namespace Outsourced\GPA\Exceptions;

use Illuminate\Http\Response;
use Outsourced\Enums\Outsourced;
use TradeAppOne\Exceptions\BuildExceptions;

class GPAExceptions
{
    public const UNAVAILABLE = 'gPAUnavailable';

    public static function unavailable($message): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UNAVAILABLE,
            'message' => trans(Outsourced::GPA . '::exceptions.' . self::UNAVAILABLE),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'description' => $message
        ]);
    }
}
