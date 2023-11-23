<?php

declare(strict_types=1);

namespace Discount\Services\Output;

class GetSaleWithimeiList0utput implements Output
{
    /** @var GetSaleWithImeiOutput[]|null */
    private $getSaleWithImeiOutput;

    /** @return GetSaleWithImeiOutput[]|null */
    public function getGetSaleWithImeiOutput(): ?array
    {
        return $this->getSaleWithImeiOutput;
    }

    public function addGetSaleWithImeiOutput(GetSaleWithImeiOutput $getSaleWithImeiOutput): self
    {
        $this->getSaleWithImeiOutput[] = $getSaleWithImeiOutput;
        return $this;
    }

    /** @return mixed[] */
    public function jsonSerialize(): array
    {
        return [
            'sales' => $this->getGetSaleWithImeiOutput()
        ];
    }
}
