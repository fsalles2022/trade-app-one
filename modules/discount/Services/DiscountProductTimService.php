<?php

declare(strict_types=1);

namespace Discount\Services;

use Discount\Models\DiscountProductTim;
use Discount\Repositories\DiscountProductTimRepository;
use Illuminate\Database\Eloquent\Collection;

class DiscountProductTimService
{
    /** @var DiscountProductTimRepository */
    protected $discountProductTimRepository;

    public function __construct(DiscountProductTimRepository $discountProductTimRepository)
    {
        $this->discountProductTimRepository = $discountProductTimRepository;
    }

    public function getAll(): Collection
    {
        return $this->discountProductTimRepository->all();
    }

    /** @param mixed[] $attributes */
    public function create(array $attributes): DiscountProductTim
    {
        return $this->discountProductTimRepository->create($attributes);
    }
}
