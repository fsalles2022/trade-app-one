<?php

namespace Core\PowerBi\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Components\Helpers\ObjectHelper;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Exceptions\BuildExceptions;

class PowerBiExceptions
{
    public const TOKEN_NOT_GENERATED = 'errorGenerateToken';
    public const UNAVAILABLE         = 'unavailable';

    public static function errorGenerateToken(Responseable $response): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::TOKEN_NOT_GENERATED,
            'message'      => trans('pbi::exceptions.' . self::TOKEN_NOT_GENERATED),
            'description'  => $response->get(),
            'httpCode'     => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function unavailable(\Exception $exception): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::UNAVAILABLE,
            'message'      => trans('pbi::exceptions.' . self::UNAVAILABLE),
            'description'  => ObjectHelper::convertToJson($exception),
            'httpCode'     => Response::HTTP_MISDIRECTED_REQUEST
        ]);
    }
}
