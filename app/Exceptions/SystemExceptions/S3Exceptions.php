<?php

namespace TradeAppOne\Exceptions\SystemExceptions;

use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BuildExceptions;

class S3Exceptions
{
    public const PUT_ERROR      = 's3PutError';
    public const DELETE_ERROR   = 's3DeleteError';
    public const DOWNLOAD_ERROR = 's3DownloadError';
    public const CONFIG_ERROR   = 's3ConfigError';

    public static function errorPut(string $description = ''): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::PUT_ERROR,
            'message' => trans('exceptions.s3.' . self::PUT_ERROR),
            'description' => $description,
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function errorDelete(string $description = ''): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::DELETE_ERROR,
            'message' => trans('exceptions.s3.' . self::DELETE_ERROR),
            'description' => $description,
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function errorDownload(string $description = ''): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::DOWNLOAD_ERROR,
            'message' => trans('exceptions.s3.' . self::DOWNLOAD_ERROR),
            'description' => $description,
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }

    public static function errorConfig(string $description = ''): BuildExceptions
    {
        return new BuildExceptions([
            'shortMessage' => self::CONFIG_ERROR,
            'message' => trans('exceptions.s3.' . self::CONFIG_ERROR),
            'description' => $description,
            'httpCode' => Response::HTTP_UNPROCESSABLE_ENTITY
        ]);
    }
}
