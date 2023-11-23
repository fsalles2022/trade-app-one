<?php

declare(strict_types=1);

namespace Reports\SubModules\Core\Models;

use TradeAppOne\Domain\Models\Tables\Hierarchy;

class Hierarchies
{
    /** @var Hierarchy[] */
    protected $hierarchies;

    /** @param Hierarchy[] $hierarchies */
    public function __construct(array $hierarchies)
    {
        $this->hierarchies = $hierarchies;
    }

    /** @return Hierarchy[] */
    public function all(): array
    {
        return $this->hierarchies;
    }

    /** @return string[] */
    public function getAllSlugs(): array
    {
        $slugs = [];

        foreach ($this->hierarchies as $hierarchy) {
            $slugs[] = $hierarchy->slug;
        }

        return array_filter($slugs);
    }
}
