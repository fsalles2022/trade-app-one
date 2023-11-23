<?php

namespace ClaroBR\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class SivAuthExceptions
{
    public const SIV_USER_ALREADY_LOGGED     = 'sivUserAlreadyLogged';
    public const SEND_PROMOTER_TOKEN         = 'sendPromoterToken';
    public const FIRST_ACCESS_PROMOTER       = 'firstAccessPromoter';
    public const DEFAULT                     = 'sivError';
    public const SELECT_PDV                  = 'sivNeedToSelectPDV';
    public const TOKEN_NOT_FOUND             = 'sivTokenNotFound';
    public const SIV_USER_NOT_FOUND          = 'sivUserNotFound';
    public const SIV_POINT_OF_SALE_NOT_FOUND = 'sivPointOfSaleNotFound';

    public static function sivUserAlreadyLogged(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::SIV_USER_ALREADY_LOGGED,
            'message'      => trans('siv::exceptions.' . self::SIV_USER_ALREADY_LOGGED),
            'httpCode'     => Response::HTTP_CONFLICT
        ]);
    }

    public static function sendPromoterToken(string $sivMessage): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage'       => self::SEND_PROMOTER_TOKEN,
            'message'            =>  trans('siv::exceptions.' . self::SEND_PROMOTER_TOKEN),
            'transportedMessage' => $sivMessage,
            'httpCode'           => Response::HTTP_PRECONDITION_FAILED
        ]);
    }

    public static function firstAccessPromoter(string $sivMessage): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage'       => self::FIRST_ACCESS_PROMOTER,
            'message'            =>  trans('siv::exceptions.' . self::FIRST_ACCESS_PROMOTER),
            'httpCode'           => Response::HTTP_PRECONDITION_FAILED,
            'transportedMessage' => $sivMessage
        ]);
    }

    public static function default(string $sivMessage): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage'       => self::DEFAULT,
            'message'            =>  $sivMessage,
            'httpCode'           => Response::HTTP_BAD_REQUEST
        ]);
    }

    public static function needSelectPDV(array $data): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage'       => self::SELECT_PDV,
            'message'            => trans('siv::exceptions.' . self::SELECT_PDV),
            'httpCode'           => Response::HTTP_PARTIAL_CONTENT,
            'transportedData'    => $data
        ]);
    }

    public static function tokenNotFound(array $data): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage'       => self::TOKEN_NOT_FOUND,
            'message'            => trans('siv::exceptions.' . self::TOKEN_NOT_FOUND),
            'httpCode'           => Response::HTTP_UNPROCESSABLE_ENTITY,
            'transportedData'    => $data
        ]);
    }

    public static function sivUserNotFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::SIV_USER_NOT_FOUND,
            'message' => trans('siv::exceptions.' . self::SIV_USER_NOT_FOUND),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }

    public static function sivPointOfSaleNotFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::SIV_POINT_OF_SALE_NOT_FOUND,
            'message' => trans('siv::exceptions.' . self::SIV_POINT_OF_SALE_NOT_FOUND),
            'httpCode' => Response::HTTP_CONFLICT
        ]);
    }
}
