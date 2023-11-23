<?php

namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class HierarchyExceptions
{
    const NOT_FOUND       = 'hierarchyNotFound';
    const WITHOUT_NETWORK = 'hierarchyWithoutNetwork';

    public static function notFound()
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_FOUND,
            'message' => trans('exceptions.hierarchy.' . self::NOT_FOUND),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }

    public static function withoutNetwork()
    {
        return new BuildExceptions([
            'shortMessage' => self::WITHOUT_NETWORK,
            'message' => trans('exceptions.hierarchy.' . self::WITHOUT_NETWORK),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
