<?php

declare(strict_types=1);

namespace TradeAppOne\Exceptions\SystemExceptions;

use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class OiResidentialSaleImportableExceptions
{
    public const SALESMAN_NOT_FOUND  = 'salesmanNotFound';
    public const SALE_ALREADY_EXISTS = 'saleAlreadyExists';

    public static function salesmanNotFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::SALESMAN_NOT_FOUND,
            'message' => trans('exceptions.oiResidentialImportable.' . self::SALESMAN_NOT_FOUND),
            'httpCode' => Response::HTTP_CONFLICT
        ]);
    }

    public static function saleAlreadyExists(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::SALE_ALREADY_EXISTS,
            'message' => trans('exceptions.oiResidentialImportable.' . self::SALE_ALREADY_EXISTS),
            'httpCode' => Response::HTTP_CONFLICT
        ]);
    }
}
