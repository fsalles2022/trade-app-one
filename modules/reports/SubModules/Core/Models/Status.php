<?php

declare(strict_types=1);

namespace Reports\SubModules\Core\Models;

class Status
{
    /** @var string[] */
    protected $status;

    /** @param string[] $status */
    public function __construct(array $status)
    {
        $this->status = $status;
    }

    /** @return string[] */
    public function all(): array
    {
        return $this->status;
    }
}
