<?php

namespace VivoBR\Tests\Unit\Headers;

use Illuminate\Support\Facades\File;
use TradeAppOne\Tests\TestCase;
use VivoBR\Connection\Headers\SunHeader;

class InstantiationHeadersTest extends TestCase
{
    /** @test */
    public function headers_should_be_resolve_by_ioc()
    {
        $directory   = base_path() . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'vivobr'
            . DIRECTORY_SEPARATOR . 'Connection' . DIRECTORY_SEPARATOR . 'Headers';
        $files       = File::allFiles($directory);
        $notInstance = ['SunHeader.php',];
        $instances   = [];
        foreach ($files as $file) {
            if (! in_array($file->getFilename(), $notInstance)) {
                $namespace = str_replace('.php', '', 'VivoBR\Connection\Headers\\' . $file->getBasename());
                array_push($instances, resolve($namespace));
            }
        }
        foreach ($instances as $instance) {
            self::assertInstanceOf(SunHeader::class, $instance);
        }
    }
}
