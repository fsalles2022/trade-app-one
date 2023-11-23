<?php


namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class ImportHistoryExceptions
{
    const FILE_NOT_FOND =  "downloadFileNotFound";

    public static function downloadFileNotFound()
    {
        return new BuildExceptions([
            'shortMessage' => self::FILE_NOT_FOND,
            'message' => trans('exceptions.import_history.' . self::FILE_NOT_FOND),
            'httpCode' => Response::HTTP_NOT_FOUND
        ]);
    }
}
