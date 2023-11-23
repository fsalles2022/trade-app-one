<?php

declare(strict_types=1);

namespace TradeAppOne\Utils\Testing;

interface ITestable
{
    public function mock(): ITestable;
    public function isMocked(): bool;
    public function isTest(): bool;
    public function asTest(): ITestable;
}
