<?php

declare(strict_types=1);

namespace Discount\Services\Output;

class UpdateImeiServiceOutput implements Output
{
    /** @var bool */
    private $success;

    public function __construct(bool $success)
    {
        $this->success = $success;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    /** @return bool[] */
    public function jsonSerialize(): array
    {
        return [
            'success' => $this->isSuccess()
        ];
    }
}
