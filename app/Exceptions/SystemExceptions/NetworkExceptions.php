<?php

namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class NetworkExceptions
{
    const NOT_BELONGS_TO_POINT_OF_SALE = 'networkNotBelongsToPointOfSale';
    const NOT_BELONGS_TO_HIERARCHY     = 'networkNotBelongsToHierarchy';
    const AVAILABLE_SERVICE_NOT_FOUND  = 'availableServiceNotFound';
    const CHANNEL_NOT_FOUND            = 'channelNotFound';
    const SLUG_ALREADY_EXISTS          = 'slugAlreadyExists';
    const AVAILABLE_SERVICES_EMPTY     = 'availableServicesEmpty';


    public static function notBelongsToPointOfSale()
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_BELONGS_TO_POINT_OF_SALE,
            'message' => trans('exceptions.network.' . self::NOT_BELONGS_TO_POINT_OF_SALE),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function notBelongsToHierarchy()
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_BELONGS_TO_HIERARCHY,
            'message' => trans('exceptions.network.' . self::NOT_BELONGS_TO_HIERARCHY),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function availableServiceNotFound()
    {
        return new BuildExceptions([
            'shortMessage' => self::AVAILABLE_SERVICE_NOT_FOUND,
            'message' => trans('exceptions.network.' . self::AVAILABLE_SERVICE_NOT_FOUND),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }

    public static function availableServicesEmpty()
    {
        return new BuildExceptions([
            'shortMessage' => self::AVAILABLE_SERVICES_EMPTY,
            'message'      => trans('exceptions.network.' . self::AVAILABLE_SERVICES_EMPTY),
            'httpCode'     => Response::HTTP_INTERNAL_SERVER_ERROR
        ]);
    }

    public static function channelNotFound()
    {
        return new BuildExceptions([
            'shortMessage' => self::CHANNEL_NOT_FOUND,
            'message' => trans('exceptions.network.' . self::CHANNEL_NOT_FOUND),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }

    public static function slugAlreadyExists()
    {
        return new BuildExceptions([
            'shortMessage' => self::SLUG_ALREADY_EXISTS,
            'message' => trans('exceptions.network.' . self::SLUG_ALREADY_EXISTS),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }
}
