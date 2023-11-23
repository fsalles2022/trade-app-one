<?php

declare(strict_types=1);

namespace Reports\SubModules\Core\Models;

use TradeAppOne\Domain\Models\Collections\Sale;

class Sales
{
    /** @var Sale[] */
    protected $sales;

    /** @param Sale[] $sales */
    public function __construct(array $sales)
    {
        $this->sales = $sales;
    }

    /** @return Sales[] */
    public function all(): array
    {
        return $this->sales;
    }
}
