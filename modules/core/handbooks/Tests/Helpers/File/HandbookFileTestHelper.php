<?php

namespace Core\HandBooks\Tests\Helpers\File;

use Illuminate\Http\UploadedFile;

class HandbookFileTestHelper
{
    public static function file(): UploadedFile
    {
        return new UploadedFile(__DIR__ . '/manual.pdf', 'manual.pdf', null, null, true);
    }
}
