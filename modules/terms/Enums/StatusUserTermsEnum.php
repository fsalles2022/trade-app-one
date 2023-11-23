<?php

declare(strict_types=1);

namespace Terms\Enums;

final class StatusUserTermsEnum
{
    public const VIEWED  = 'VIEWED';
    public const CHECKED = 'CHECKED';

    public const AVAILABLE_STATUS = [
        self::VIEWED,
        self::CHECKED
    ];
}
