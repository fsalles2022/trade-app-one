<?php

declare(strict_types=1);

namespace Discount\Repositories\DTOs;

interface Dto
{
    /** @return mixed[] */
    public function toArray(): array;
}
