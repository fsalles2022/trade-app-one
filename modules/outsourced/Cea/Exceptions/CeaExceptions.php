<?php


namespace Outsourced\Cea\Exceptions;

use Illuminate\Http\Response;
use TradeAppOne\Domain\Components\Helpers\ObjectHelper;
use TradeAppOne\Exceptions\BuildExceptions;

final class CeaExceptions
{
    public const DEFAULT          = 'ceaClientUnavailable';
    public const CARD_UNAVAILABLE = 'cardUnavailable';

    public static function default($exception): BuildExceptions
    {
        $message = $exception->getMessage() . ': ' . data_get($exception, 'detail.BusinessFault.mensagem');

        return new BuildExceptions([
            'shortMessage'        =>  self::DEFAULT,
            'message'             =>  $message,
            'transportedMessage'  =>  ObjectHelper::convertToJson($exception),
            'httpCode'            =>  Response::HTTP_MISDIRECTED_REQUEST
        ]);
    }

    public static function cardsUnavailable()
    {
        return new BuildExceptions([
            'shortMessage'        =>  self::CARD_UNAVAILABLE,
            'message'             =>  trans('cea::exceptions.' . self::CARD_UNAVAILABLE),
            'httpCode'            =>  Response::HTTP_NOT_FOUND
        ]);
    }
}
