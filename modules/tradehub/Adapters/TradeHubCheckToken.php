<?php

declare(strict_types=1);

namespace Tradehub\Adapters;

class TradeHubCheckToken extends TradeHubPayloadAdapter
{
    /** @var string|null */
    private $phone;

    /** @var string|null */
    private $code;

    /** @var string|null */
    private $origin;

    public function __construct(?string $phone, ?string $code, ?string $origin)
    {
        $this->phone  = $phone;
        $this->code   = $code;
        $this->origin = $origin;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phone;
    }

    /**
     * @return string|null
     */
    public function getCodeAuthorization(): ?string
    {
        return $this->code;
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
            'code' => $this->getCodeAuthorization(),
            'origin' => $this->getOrigin(),
        ];
    }
}
