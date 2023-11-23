<?php

namespace TradeAppOne\Exceptions;

use Illuminate\Http\Response;

final class ImportableExceptions
{
    const USER_CANNOT_ADD_TO_NETWORK = 'userCanNotAddToNetwork';
    const REGISTER_ALREADY_EXISTS    = 'registerAlreadyExists';

    public static function userCanNotAddToNetwork(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::USER_CANNOT_ADD_TO_NETWORK,
            'message' => trans('exceptions.' . self::USER_CANNOT_ADD_TO_NETWORK),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function registerAlreadyExists(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::REGISTER_ALREADY_EXISTS,
            'message' => trans('exceptions.' . self::REGISTER_ALREADY_EXISTS),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
