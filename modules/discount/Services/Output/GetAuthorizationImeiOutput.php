<?php

declare(strict_types=1);

namespace Discount\Services\Output;

class GetAuthorizationImeiOutput implements Output
{
    /** @var string|null */
    private $hash;

    public function __construct(?string $hash)
    {
        $this->hash = $hash;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    /** @return null[]|string[] */
    public function jsonSerialize(): array
    {
        return [
            'token' => $this->getHash()
        ];
    }
}
