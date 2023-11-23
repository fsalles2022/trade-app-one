<?php


namespace TradeAppOne\Domain\Components\Helpers;

class FilePathFromUrl
{
    public static function extractS3Path($url)
    {
        return parse_url($url, PHP_URL_PATH);
    }
}
