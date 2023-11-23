<?php

declare(strict_types=1);

namespace Reports\SubModules\Core\Models;

class Operations
{
    /** @var string[] */
    protected $operations;

    /** @param string[] $operations */
    public function __construct(array $operations)
    {
        $this->operations = $operations;
    }

    /** @return string[] */
    public function all(): array
    {
        return $this->operations;
    }
}
