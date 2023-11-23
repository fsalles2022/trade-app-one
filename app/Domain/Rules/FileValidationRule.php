<?php

namespace TradeAppOne\Domain\Rules;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use TradeAppOne\Domain\Enumerators\Files\FileExtensions;

class FileValidationRule extends Rule
{
    const PDF     = '10485760'; //10MB
    const MP4     = '20971520'; //20MB
    const DEFAULT = '10485760'; //10MB

    public function passes($key, UploadedFile $file)
    {
        $size = $file->getSize();
        $type = $file->getClientOriginalExtension();

        switch ($type) {
            case FileExtensions::PDF:
                return $size <= self::PDF;

            case FileExtensions::MP4:
                return $size <= self::MP4;

            default:
                return $size <= self::DEFAULT;
        }
    }

    public function message()
    {
        return trans('validation.file_size');
    }
}
