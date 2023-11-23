<?php

declare(strict_types=1);

namespace TradeAppOne\Rules\Utils;

class ValueReplacerFormat
{
    /**
     * @var string
     */
    protected $subject;
    /**
     * @var string
     */
    protected $valuePlaceholder;
    /**
     * @var mixed|null
     */
    protected $value;

    public function __construct(string $subject, $value = null, string $valuePlaceholder = ':value')
    {
        $this->subject          = $subject;
        $this->valuePlaceholder = $valuePlaceholder;
        $this->value            = $value;
    }

    public function apply(): string
    {
        return str_replace($this->valuePlaceholder, $this->value ?? '', $this->subject);
    }

    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }
}
