<?php

namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

final class ServiceExceptions
{
    const CANNOT_BE_CANCEL         = 'serviceCannotBeCancel';
    const NOT_FOUND                = 'serviceNotFound';
    const ACTIVE_TO_CANCEL         = 'serviceActiveToCancel';
    const CANCELLATION_EXPIRED     = 'serviceCancellationExpired';
    const ALREADY_CANCELLED        = 'serviceAlreadyCancelled';
    const NEEDS_ACCEPTED_TO_CANCEL = 'serviceNeedsAcceptedToCancel';
    const TOKEN_CARD_NOT_FOUND     = 'tokenCardNotFoundInService';

    public static function SERVICE_CANNOT_BE_CONTESTED(): BuildExceptions
    {
        throw new BuildExceptions([
            'shortMessage' => 'ServiceCannotBeContested',
            'message' => trans('exceptions.service.canot_be_contested'),
            'httpCode' => Response::HTTP_NOT_ACCEPTABLE
        ]);
    }

    public static function cannotBeCancel(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::CANNOT_BE_CANCEL,
            'message' => trans('exceptions.service.' . self::CANNOT_BE_CANCEL),
            'httpCode' => Response::HTTP_NOT_ACCEPTABLE
        ]);
    }

    public static function notFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NOT_FOUND,
            'message' => trans('exceptions.service.' . self::NOT_FOUND),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }

    public static function activeToCancel(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::ACTIVE_TO_CANCEL,
            'message' => trans('exceptions.service.' . self::ACTIVE_TO_CANCEL),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function cancellationExpired(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::CANCELLATION_EXPIRED,
            'message' => trans('exceptions.service.' . self::CANCELLATION_EXPIRED),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function alreadyCancelled($serviceType): BuildExceptions
    {
        return new BuildExceptions([
           'shortMessage' => self::ALREADY_CANCELLED,
           'message'      => trans('exceptions.service.' . self::ALREADY_CANCELLED, [
               'serviceType'   => trans('operations.' . $serviceType)
           ]),
           'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function needsAcceptedToCancel(?string $status): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::NEEDS_ACCEPTED_TO_CANCEL,
            'message' => trans('exceptions.service.'. self::NEEDS_ACCEPTED_TO_CANCEL, [
                'status' => $status
            ]),
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function tokenCardNotFound(): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::TOKEN_CARD_NOT_FOUND,
            'message'      => trans('exceptions.service.'. self::TOKEN_CARD_NOT_FOUND),
            'httpCode'     => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
