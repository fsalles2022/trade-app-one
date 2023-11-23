<?php

declare(strict_types=1);

namespace ClaroBR\Adapters;

class Siv3CheckAuthorizationCode extends Siv3PayloadAdapter
{
    /** @var string|null */
    private $phoneNumber;

    /** @var string|null */
    private $codeAuthorization;

    public function __construct(?string $phoneNumber, ?string $codeAuthorization)
    {
        $this->phoneNumber       = $phoneNumber;
        $this->codeAuthorization = $codeAuthorization;
    }

    public function getPhoneNumber(): string
    {
        return '+55' . $this->phoneNumber;
    }

    public function getCodeAuthorization(): ?string
    {
        return $this->codeAuthorization;
    }

    /** @return string[] */
    public function jsonSerialize(): array
    {
        return [
            'phoneNumber' => $this->getPhoneNumber(),
            'codeAuthorization' => $this->getCodeAuthorization()
        ];
    }
}
