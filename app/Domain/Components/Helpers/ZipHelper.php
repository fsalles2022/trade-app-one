<?php


namespace TradeAppOne\Domain\Components\Helpers;

use ZipArchive;

class ZipHelper
{
    public static function create($zip_file): \ZipArchive
    {
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        return $zip;
    }

    public function close(\ZipArchive $zipArchive): bool
    {
        return $zipArchive->close();
    }
}
