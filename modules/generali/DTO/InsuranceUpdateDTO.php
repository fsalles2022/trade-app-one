<?php


namespace Generali\DTO;

class InsuranceUpdateDTO
{
    public $reference;
    public $status;
    public $extra;

    public function __construct(string $reference, string $status, array $extra = [])
    {
        $this->reference = $reference;
        $this->status    = $status;
        $this->extra     = $extra;
    }

//    public function getReference(): string
//    {
//        return $this->reference;
//    }
//
//    public function setReference($reference): void
//    {
//        $this->reference = $reference;
//    }
//
//    public function getStatus(): string
//    {
//        return $this->status;
//    }
//
//    public function setStatus($status): void
//    {
//        $this->status = $status;
//    }
//
//    public function getExtra(): array
//    {
//        return $this->extra;
//    }
//
//    public function setExtra($extra): void
//    {
//        $this->extra = $extra;
//    }
}
