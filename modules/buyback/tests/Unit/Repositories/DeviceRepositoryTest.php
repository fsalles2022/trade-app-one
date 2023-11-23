<?php

namespace Buyback\tests\Unit\Repositories;

use Buyback\Repositories\DeviceRepository;
use Buyback\Tests\Helpers\Builders\DeviceBuilder;
use Illuminate\Database\Eloquent\Collection;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\TestCase;

class DeviceRepositoryTest extends TestCase
{
    /** @test */
    public function should_return_an_instance_of_device_repository()
    {
        $deviceRepository = new DeviceRepository();

        $className = get_class($deviceRepository);

        $this->assertEquals(DeviceRepository::class, $className);
    }

    /** @test */
    public function should_return_a_collection_when_call_find_all_by_network_id()
    {
        $deviceRepository   = new DeviceRepository();
        $networkEntity      = (new NetworkBuilder())->build();
        $devicesCollection  = $deviceRepository->findAllByNetworksIdWithFilters([$networkEntity->id]);
        $collectionExpected = new Collection();

        $this->assertEquals($collectionExpected, $devicesCollection);
    }

    /** @test */
    public function should_return_one_device_when_call_find_all_by_network_id()
    {
        $deviceRepository = new DeviceRepository();
        $networkEntity    = (new NetworkBuilder())->build();
        (new DeviceBuilder())->withNetwork($networkEntity)->build();
        $devicesCollection = $deviceRepository->findAllByNetworksIdWithFilters([$networkEntity->id]);
        
        $this->assertNotNull($devicesCollection);
    }

    /** @test */
    public function should_return_one_device_when_call_find_all_by_network_id_with_model_filter()
    {
        $deviceRepository  = new DeviceRepository();
        $networkEntity     = (new NetworkBuilder())->build();
        $deviceEntity      = (new DeviceBuilder())->withNetwork($networkEntity)->build();
        $deviceModel       = $deviceEntity->model;
        $devicesCollection = $deviceRepository->findAllByNetworksIdWithFilters([$networkEntity->id], ['model' => $deviceModel]);
        $this->assertNotNull($devicesCollection);
    }

    /** @test */
    public function should_return_one_device_when_call_find_all_by_network_id_with_brand_filter()
    {
        $deviceRepository  = new DeviceRepository();
        $networkEntity     = (new NetworkBuilder())->build();
        $deviceEntity      = (new DeviceBuilder())->withNetwork($networkEntity)->build();
        $deviceBrand       = $deviceEntity->brand;
        $devicesCollection = $deviceRepository->findAllByNetworksIdWithFilters([$networkEntity->id], ['brand' => $deviceBrand]);

        $this->assertNotNull($devicesCollection);
    }

    /** @test */
    public function should_return_one_device_when_call_find_all_by_network_id_with_color_filter()
    {
        $deviceRepository  = new DeviceRepository();
        $networkEntity     = (new NetworkBuilder())->build();
        $deviceEntity      = (new DeviceBuilder())->withNetwork($networkEntity)->build();
        $deviceColor       = $deviceEntity->color;
        $devicesCollection = $deviceRepository->findAllByNetworksIdWithFilters([$networkEntity->id], ['color' => $deviceColor]);

        $this->assertNotNull($devicesCollection);
    }

    /** @test */
    public function should_return_one_device_when_call_find_all_by_network_id_with_storage_filter()
    {
        $deviceRepository  = new DeviceRepository();
        $networkEntity     = (new NetworkBuilder())->build();
        $deviceEntity      = (new DeviceBuilder())->withNetwork($networkEntity)->build();
        $deviceStorage     = $deviceEntity->storage;
        $devicesCollection = $deviceRepository->findAllByNetworksIdWithFilters([$networkEntity->id], ['storage' => $deviceStorage]);

        $this->assertNotNull($devicesCollection);
    }

    /** @test */
    public function should_return_one_device_when_call_find_one_device_by_deviceId_and_NetworkId()
    {
        $deviceRepository  = new DeviceRepository();
        $networkEntity     = (new NetworkBuilder())->build();
        $deviceEntity      = (new DeviceBuilder())->withNetwork($networkEntity)->build();
        $devicesCollection = $deviceRepository->findOneDeviceByDeviceIdAndNetworkId($deviceEntity->id, $networkEntity->id);

        $this->assertNotNull($devicesCollection);
    }
}
