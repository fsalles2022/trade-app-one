<?php

declare(strict_types=1);

namespace Outsourced\CasaEVideo\Exceptions;

use Illuminate\Http\Response;
use Outsourced\Enums\Outsourced;
use TradeAppOne\Exceptions\BuildExceptions;

class CasaEVideoExpcetions
{
    public const ERROR_TO_SEND_SALE_WEBHOOK = 'errorToSendSaleWebhook';

    public static function errorToSendSaleWebhook(?string $message): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::ERROR_TO_SEND_SALE_WEBHOOK,
            'message' => trans(Outsourced::CASAEVIDEO . '::exceptions.' . self::ERROR_TO_SEND_SALE_WEBHOOK),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'description' => $message
        ]);
    }
}
