<?php

declare(strict_types=1);

namespace Tradehub\Adapters;

class TradeHubSendToken extends TradeHubPayloadAdapter
{
    /** @var string|null */
    private $phone;

    /** @var string|null */
    private $origin;

    public function __construct(?string $phone, ?string $origin)
    {
        $this->phone  = $phone;
        $this->origin = $origin;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phone;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    /** @return string[] */
    public function jsonSerialize(): array
    {
        return [
            'phone' => $this->getPhoneNumber(),
            'origin' => $this->getOrigin(),
        ];
    }
}
