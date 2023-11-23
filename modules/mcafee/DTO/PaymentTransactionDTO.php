<?php

declare(strict_types=1);

namespace McAfee\DTO;

use Gateway\Enumerators\StatusPaymentTransaction;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;

class PaymentTransactionDTO implements Arrayable
{
    /** @var int|null */
    private $status;

    /** @var int */
    private $createdAt;

    /** @param string[]|int[] $paymentTransaction */
    public function __construct(array $paymentTransaction)
    {
        $this->status    = (int) $paymentTransaction['status'] ?? 999;
        $this->createdAt = (int) $paymentTransaction['create_time'] ?? now()->timestamp;
    }

    public function getStatus(): string
    {
        if (array_key_exists($this->status, StatusPaymentTransaction::STATUS_PAYMENT)) {
            return StatusPaymentTransaction::STATUS_PAYMENT[$this->status];
        }
        return StatusPaymentTransaction::UNKNOWN_STATUS;
    }

    public function getCreatedAt(): string
    {
        return date(DATE_ATOM, $this->createdAt);
    }

    /** @return string[] */
    public function toArray(): array
    {
        return [
            'status' => $this->getStatus(),
            'createdAt' => $this->getCreatedAt(),
        ];
    }
}
