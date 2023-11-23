<?php

namespace Outsourced\ViaVarejo\tests\Unit;

use Outsourced\Crafts\Devices\OutsourcedDeviceDTO;
use Outsourced\Crafts\Services\DevicesGeneralService;
use Outsourced\ViaVarejo\tests\ViaVarejoTestBook;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\TestCase;

class DevicesViaVarejoTest extends TestCase
{
    /** @test */
    public function should_return_device_by_sku(): void
    {
        $successSku = ViaVarejoTestBook::SUCCESS_SKU;

        $network = factory(Network::class)->create(['slug' => NetworkEnum::VIA_VAREJO]);
        $network->save();

        factory(DeviceOutSourced::class)->create([
            'sku' => $successSku,
            'networkId' => $network->id
        ])->save();

        $devicesViaVarejo = resolve(DevicesGeneralService::class);
        $outsourcedDTO    = $devicesViaVarejo->findDevice($successSku);

        $this->assertInstanceOf(OutsourcedDeviceDTO::class, $outsourcedDTO);
        $this->assertEquals($successSku, $outsourcedDTO->sku);
        $this->assertNotEmpty($outsourcedDTO->label);
        $this->assertNotEmpty($outsourcedDTO->model);
    }

    /** @test */
    public function should_not_return_device_by_invalid_sku(): void
    {
        $invalidSku = ViaVarejoTestBook::INVALID_SKU;

        $network = factory(Network::class)->create(['slug' => NetworkEnum::VIA_VAREJO]);
        $network->save();

        factory(DeviceOutSourced::class)->create([
            'sku' => ViaVarejoTestBook::SUCCESS_SKU,
            'networkId' => $network->id
        ])->save();

        $devicesViaVarejo = resolve(DevicesGeneralService::class);
        $outsourcedDTO    = $devicesViaVarejo->findDevice($invalidSku);

        $this->assertNull($outsourcedDTO->sku);
        $this->assertNull($outsourcedDTO->label);
        $this->assertNull($outsourcedDTO->model);
    }
}
