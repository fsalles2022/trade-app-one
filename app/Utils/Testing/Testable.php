<?php

declare(strict_types=1);

namespace TradeAppOne\Utils\Testing;

trait Testable
{
    private $isMock = false;
    private $isTest = false;

    public function mock(): ITestable
    {
        $this->isMock = true;
        return $this;
    }

    public function isMocked(): bool
    {
        return $this->isMock;
    }

    public function isTest(): bool
    {
        return $this->isTest;
    }

    public function asTest(): ITestable
    {
        $this->isTest = true;

        return $this;
    }
}
