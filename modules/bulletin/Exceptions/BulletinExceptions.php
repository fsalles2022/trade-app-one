<?php

declare(strict_types=1);

namespace Bulletin\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class BulletinExceptions
{
    public const NOT_FOUND              = 'notFound';
    public const ACTIVATION_NOT_UPDATED = 'activationNotUpdated';

    /** @return BuildExceptions */
    public static function notFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage'      => self::NOT_FOUND,
            'message'           => trans('bulletin::exceptions.' . self::NOT_FOUND),
            'httpCode'          => Response::HTTP_UNPROCESSABLE_ENTITY,
        ]);
    }

    /** @return BuildExceptions */
    public static function activationNotUpdated(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage'      => self::ACTIVATION_NOT_UPDATED,
            'message'           => trans('bulletin::exceptions.' . self::ACTIVATION_NOT_UPDATED),
            'httpCode'          => Response::HTTP_UNPROCESSABLE_ENTITY,
        ]);
    }
}
