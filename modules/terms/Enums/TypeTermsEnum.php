<?php

declare(strict_types=1);

namespace Terms\Enums;

final class TypeTermsEnum
{
    public const CUSTOMER = 'customer';
    public const SALESMAN = 'salesman';

    public const TERM_TYPE = [
        self::CUSTOMER,
        self::SALESMAN
    ];
}
