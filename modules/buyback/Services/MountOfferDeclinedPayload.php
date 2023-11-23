<?php

namespace Buyback\Services;

use TradeAppOne\Domain\Models\Tables\Device;
use TradeAppOne\Domain\Models\Tables\User;

class MountOfferDeclinedPayload
{
    private $customer;
    private $reason;
    private $questions;
    private $noteAndPrice;
    private $device;
    private $deviceImei;
    private $weight;
    private $operator;
    private $operation;

    public function addCustomer(array $customer): MountOfferDeclinedPayload
    {
        $this->customer = $customer;
        return $this;
    }

    public function addReason(string $reason): MountOfferDeclinedPayload
    {
        $this->reason = $reason;
        return $this;
    }

    public function addUser(User $user): MountOfferDeclinedPayload
    {
        $this->user = $user;
        return $this;
    }

    public function addQuestions(array $questions): MountOfferDeclinedPayload
    {
        $this->questions = $questions;
        return $this;
    }

    public function addImei(string $deviceImei): MountOfferDeclinedPayload
    {
        $this->deviceImei = $deviceImei;
        return $this;
    }

    public function addPrice(int $noteAndPrice): MountOfferDeclinedPayload
    {
        $this->noteAndPrice = $noteAndPrice;
        return $this;
    }

    public function addWeight(int $noteAndPrice): MountOfferDeclinedPayload
    {
        $this->weight = $noteAndPrice;
        return $this;
    }

    public function addDevice(Device $device): MountOfferDeclinedPayload
    {
        $this->device = $device;
        return $this;
    }

    public function addOperator(String $operator): MountOfferDeclinedPayload
    {
        $this->operator = $operator;
        return $this;
    }

    public function addOperation(String $operation): MountOfferDeclinedPayload
    {
        $this->operation = $operation;
        return $this;
    }

    public function mount()
    {
        return [
            'customer' => $this->customer,
            'reason' => $this->reason,
            'questions' => $this->questions,
            'pointOfSale' => $this->getPointOfSale(),
            'user' => $this->getUser(),
            'device' => $this->getDevice(),
            'operator' => $this->operator,
            'operation' => $this->operation
        ];
    }

    private function getPointOfSale(): array
    {
        $pointOfSale = $this->user->pointsOfSale()->with('network')->first()->toArray();
        return array_filter($pointOfSale);
    }

    private function getUser(): array
    {
        return array_filter($this->user->toArray());
    }

    private function getDevice(): array
    {
        return array_merge($this->device->toArray(), [
            'price' => $this->noteAndPrice,
            'note' => $this->weight,
            'imei' => $this->deviceImei,
        ]);
    }
}
