<?php

declare(strict_types=1);

namespace Discount\Repositories\DTOs;

class ImeiChangeHistoryCreateDto implements Dto
{
    /** @var string|null */
    private $serviceTransaction;

    /** @var string|null */
    private $oldImei;

    /** @var string|null */
    private $newImei;

    /** @var int|null */
    private $userIdWhoChanged;

    /** @var string|null */
    private $userCpfWhoChanged;

    /** @var int|null */
    private $userIdWhoAuthorized;

    /** @var string|null */
    private $userCpfWhoAuthorized;

    /** @var string|null */
    private $exchangeDate;

    /** @var string|null */
    private $protocol;

    public function __construct(
        ?string $serviceTransaction,
        ?string $oldImei,
        ?string $newImei,
        ?int $userIdWhoChanged,
        ?string $userCpfWhoChanged,
        ?int $userIdWhoAuthorized,
        ?string $userCpfWhoAuthorized,
        ?string $exchangeDate,
        ?string $protocol
    ) {
        $this->serviceTransaction   = $serviceTransaction;
        $this->oldImei              = $oldImei;
        $this->newImei              = $newImei;
        $this->userIdWhoChanged     = $userIdWhoChanged;
        $this->userCpfWhoChanged    = $userCpfWhoChanged;
        $this->userIdWhoAuthorized  = $userIdWhoAuthorized;
        $this->userCpfWhoAuthorized = $userCpfWhoAuthorized;
        $this->exchangeDate         = $exchangeDate;
        $this->protocol             = $protocol;
    }

    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    public function getServiceTransaction(): ?string
    {
        return $this->serviceTransaction;
    }

    public function getExchangeDate(): ?string
    {
        return $this->exchangeDate;
    }

    public function getNewImei(): ?string
    {
        return $this->newImei;
    }

    public function getOldImei(): ?string
    {
        return $this->oldImei;
    }

    public function getUserCpfWhoAuthorized(): ?string
    {
        return $this->userCpfWhoAuthorized;
    }

    public function getUserCpfWhoChanged(): ?string
    {
        return $this->userCpfWhoChanged;
    }

    public function getUserIdWhoAuthorized(): ?int
    {
        return $this->userIdWhoAuthorized;
    }

    public function getUserIdWhoChanged(): ?int
    {
        return $this->userIdWhoChanged;
    }

    /** @inheritDoc */
    public function toArray(): array
    {
        return [
            'serviceTransaction' => $this->getServiceTransaction(),
            'oldImei' => $this->getOldImei(),
            'newImei' => $this->getNewImei(),
            'userIdWhoChanged' => $this->getUserIdWhoChanged(),
            'userCpfWhoChanged' => $this->getUserCpfWhoChanged(),
            'userIdWhoAuthorized' => $this->getUserIdWhoAuthorized(),
            'userCpfWhoAuthorized' => $this->getUserCpfWhoAuthorized(),
            'exchangeDate' => $this->getExchangeDate(),
            'protocol' => $this->getProtocol()
        ];
    }
}
