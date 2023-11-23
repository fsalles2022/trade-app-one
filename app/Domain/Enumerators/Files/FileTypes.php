<?php

namespace TradeAppOne\Domain\Enumerators\Files;

final class FileTypes
{
    public const DOCUMENT = 'DOCUMENT';
    public const VIDEO    = 'VIDEO';

    public const TYPES = [
        FileExtensions::PDF => self::DOCUMENT,
        FileExtensions::MP4 => self::VIDEO
    ];
}
