<?php

namespace TradeAppOne\Facades;

use Illuminate\Support\Facades\Facade;
use TradeAppOne\Domain\Components\Helpers\ZipHelper;

/**
 * @method static \ZipArchive create(string $pathname)
 * @method static archive(string $fileNames, string $pathName)
 * @method static string getDiskFullPathName(string $diskName, string $pathName)
 * @method static bool close(\ZipArchive $zipFile)
 *
 * @see ZipHelper
 */
class ZipFiles extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ZipHelper::class;
    }
}
