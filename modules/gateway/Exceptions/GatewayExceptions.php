<?php

namespace Gateway\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class GatewayExceptions
{
    const GATEWAY_UNAVAILABLE              = 'gatewayUnavailable';
    const GATEWAY_TRANSACTION_NOT_APPROVED = 'gatewayTransactionNotApproved';
    const GATEWAY_ERROR_CANCELING_THE_SALE = 'gatewayErrorCancelingTheSale';
    const CARD_UNAUTHORIZED                = 'gatewayCardUnauthorized';
    const TOKEN_CARD_INVALID               = 'gatewayTokenCardInvalid';
    const TRANSACTION_ID_NOT_FOUND         = 'gatewayTransactionIdNotFound';

    public static function gatewayUnavailable(?string $gatewayMessage = '')
    {
        return new BuildExceptions([
            'shortMessage' => self::GATEWAY_UNAVAILABLE,
            'message' => trans('gateway::exceptions.' . self::GATEWAY_UNAVAILABLE),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'transportedMessage' => $gatewayMessage
        ]);
    }

    public static function gatewayTransactionNotApproved()
    {
        return new BuildExceptions([
            'shortMessage' => self::GATEWAY_TRANSACTION_NOT_APPROVED,
            'message' => trans('gateway::exceptions.' . self::GATEWAY_TRANSACTION_NOT_APPROVED),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function gatewayErrorCancelingTheSale()
    {
        return new BuildExceptions([
            'shortMessage' => self::GATEWAY_ERROR_CANCELING_THE_SALE,
            'message' => trans('gateway::exceptions.' . self::GATEWAY_ERROR_CANCELING_THE_SALE),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function cardUnauthorized()
    {
        return new BuildExceptions([
            'shortMessage' => self::CARD_UNAUTHORIZED,
            'message' => trans('gateway::exceptions.' . self::CARD_UNAUTHORIZED),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function tokenCardInvalid(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::TOKEN_CARD_INVALID,
            'message' => trans('gateway::exceptions.' . self::TOKEN_CARD_INVALID),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function transactionIdNotFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::TRANSACTION_ID_NOT_FOUND,
            'message' => trans('gateway::exceptions.' . self::TRANSACTION_ID_NOT_FOUND),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
