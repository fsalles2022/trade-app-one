<?php

declare(strict_types=1);

namespace Discount\Repositories;

use Discount\Models\DiscountProductTim;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class DiscountProductTimRepository extends BaseRepository
{
    protected $model = DiscountProductTim::class;
}
