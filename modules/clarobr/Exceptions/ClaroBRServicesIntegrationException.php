<?php


namespace ClaroBR\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class ClaroBRServicesIntegrationException extends BuildExceptions
{
    public const INTEGRATOR_URL_NOT_FOUND = 'integratorUrlNotFound';

    public static function integratorUrlNotFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::INTEGRATOR_URL_NOT_FOUND,
            'message'      => trans('siv::exceptions.' . self::INTEGRATOR_URL_NOT_FOUND),
            'httpCode'     => Response::HTTP_CONFLICT
        ]);
    }
}
