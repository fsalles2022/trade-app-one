<?php


namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class PointOfSaleExceptions
{
    public const CNPJ_ALREADY_EXISTS             = "cnpjAlreadyExists";
    public const NOT_FOUND                       = 'pointOfSaleNotFound';
    public const NOT_FOUND_POINT_OF_SALE_NETWORK = 'notFoundPointOfSaleNetwork';

    public static function cnpjAlreadyExists()
    {
        return new BuildExceptions([
            'shortMessage' => self::CNPJ_ALREADY_EXISTS,
            'message' => trans('exceptions.pos.' . self::CNPJ_ALREADY_EXISTS),
            'httpCode' => Response::HTTP_CONFLICT
        ]);
    }

    public static function pointOfSaleNotFound()
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_FOUND,
            'message'      => trans('exceptions.pos.not_found'),
            'httpCode'     => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function notFoundPointOfSaleNetwork(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_FOUND_POINT_OF_SALE_NETWORK,
            'message'      => trans('exceptions.pos.network_no_exists'),
            'httpCode'     => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
