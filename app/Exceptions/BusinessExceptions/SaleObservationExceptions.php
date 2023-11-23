<?php

declare(strict_types=1);

namespace TradeAppOne\Exceptions\BusinessExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class SaleObservationExceptions
{
    const OBSERVATION_ALREADY_EXISTS = "observationAlreadyExists";

    public static function observationAlreadyExists(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::OBSERVATION_ALREADY_EXISTS,
            'message' => 'Observação já inserida nesse serviço.',
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
