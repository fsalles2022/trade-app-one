<?php

declare(strict_types=1);

namespace TradeAppOne\Utils\File;

interface IAttachable
{
    /**
     * the File's path
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * generate/prepare file
     *
     * @return string[]
     */
    public function options(): array;
}
