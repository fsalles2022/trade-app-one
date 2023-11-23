<?php

namespace Banner\Enumerators;

final class BannerStatus
{
    const PUBLISHED   = 'PUBLISHED';
    const UNPUBLISHED = 'UNPUBLISHED';
    const WAITING     = 'WAITING';

    const STATUS = [self::PUBLISHED, self::UNPUBLISHED, self::WAITING];
}
