<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class SalesSimulatorResidentialException
{
    public const ADDRESS_NOT_EXISTS = 'addressNotExists';

    public static function addressNotExists(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::ADDRESS_NOT_EXISTS,
            'message' => trans('simulatorClaroResidential::exceptions.' . self::ADDRESS_NOT_EXISTS),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }
}
