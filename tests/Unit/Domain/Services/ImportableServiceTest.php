<?php

namespace TradeAppOne\Tests\Unit\Domain\Services;

use Buyback\Tests\Helpers\Builders\DeviceBuilder;
use TradeAppOne\Domain\Services\ImportableService;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ImportableServiceTest extends TestCase
{
    /** @test */
    public function should_return_with_slugfy_label()
    {
        $device  = (new DeviceBuilder())->build();
        $user    = (new UserBuilder())->withNetwork($device->networks->first())->build();
        $service = resolve(ImportableService::class);
        $result  = $service->getNetworkDevices($user);
    }
}