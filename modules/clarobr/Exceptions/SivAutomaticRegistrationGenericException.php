<?php
declare(strict_types=1);

namespace ClaroBR\Exceptions;

use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Exceptions\BuildExceptions;

class SivAutomaticRegistrationGenericException extends BuildExceptions
{
    public function __construct(?string $message = null, Responseable $response)
    {
        parent::__construct([
            'shortMessage' => SivAutomaticRegistrationExceptions::GENERIC_SIV_ERROR,
            'message'      => trans($message),
            'httpCode'     => $response->getStatus(),
            'description'  => $message
        ]);
    }
}
