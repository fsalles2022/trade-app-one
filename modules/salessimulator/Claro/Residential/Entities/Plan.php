<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Entities;

use SalesSimulator\Claro\Residential\Collections\CollectionInterface;

class Plan implements CollectionInterface
{
    /** @var int|null */
    private $id;

    /** @var string|null */
    private $label;

    /** @var string|null */
    private $description;

    /** @var float|null */
    private $price;

    /** @var string|null */
    private $type;

    /** @var Promotion[]|null */
    private $promotions;

    public function __construct(
        ?int $id,
        ?string $label,
        ?string $description,
        ?float $price,
        ?string $type,
        ?array $promotions
    ) {
        $this->id          = $id ?? null;
        $this->label       = $label ?? null;
        $this->description = $description ?? null;
        $this->price       = $price ?? null;
        $this->type        = $type ?? null;
        $this->promotions  = $promotions ?? null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    /** @return Promotion[]|null */
    public function getPromotions(): ?array
    {
        return $this->promotions;
    }

    /** @return mixed[] */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'description' => $this->description,
            'price' => $this->price,
            'type' => $this->type,
            'promotions' => $this->promotions
        ];
    }
}
