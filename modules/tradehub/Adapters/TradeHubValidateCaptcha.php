<?php

declare(strict_types=1);

namespace Tradehub\Adapters;

class TradeHubValidateCaptcha extends TradeHubPayloadAdapter
{
    /** @var string|null */
    private $code;

    /** @var string|null */
    private $key;

    public function __construct(?string $code, ?string $key)
    {
        $this->code = $code;
        $this->key = $key;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /** @return string[] */
    public function jsonSerialize(): array
    {
        return [
            'text' => $this->getCode(),
            'key'  => $this->getKey()
        ];
    }
}
