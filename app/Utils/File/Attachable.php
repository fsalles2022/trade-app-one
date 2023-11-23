<?php

declare(strict_types=1);

namespace TradeAppOne\Utils\File;

use Illuminate\Http\File;

abstract class Attachable extends File implements IAttachable
{
    public function getPath(): string
    {
        return $this->path();
    }
}
