<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Entities;

use SalesSimulator\Claro\Residential\Collections\CollectionInterface;

class Promotion implements CollectionInterface
{
    /** @var int|null */
    private $id;

    /** @var string|null */
    private $label;

    /** @var float|null */
    private $price;

    public function __construct(?int $id, ?string $label, ?string $price)
    {
        $this->id    = $id ?? null;
        $this->label = $label ?? null;
        $this->price = is_numeric($price) ? (float) $price : null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    /** @return mixed[] */
    public function jsonSerialize(): array
    {
        return [
            'id'    => $this->id,
            'label' => $this->label,
            'price' => $this->price,
        ];
    }
}
