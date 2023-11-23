<?php

namespace TradeAppOne\Facades;

use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpFoundation\StreamedResponse;
use TradeAppOne\Domain\Components\Storage\S3Service;

/**
 * @method static string put(string $path, $file, $options)
 * @method static bool delete(string $filePath)
 * @method static string url(string $filePath)
 * @method static StreamedResponse download(string $filePath)
 *
 * @see S3Service
 */

class S3 extends Facade
{
    protected static function getFacadeAccessor()
    {
        return S3Service::class;
    }
}
