<?php

namespace VivoBR\Tests\Unit\Console;

use Illuminate\Foundation\Console\Kernel;

class KernelTest extends Kernel
{
    public function registerCommand($command)
    {
        $this->getArtisan()->add($command);
    }
}
