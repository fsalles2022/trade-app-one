<?php

namespace TradeAppOne\Tests\Unit\Domain\Importables;

use TradeAppOne\Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Exceptions\ImportableExceptions;
use Buyback\Tests\Helpers\Builders\DeviceBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Domain\Importables\DevicesNetworkImportable;

class DevicesNetworkImportableTest extends TestCase
{
    use ArrayAssertTrait;

    /** @test */
    public function should_get_columns_return_correct_structure()
    {
        $class = new DevicesNetworkImportable();

        $result = $class->getColumns();

        $this->assertArrayStructure($result, [
            "deviceId",
            "networkId",
            "sku"
        ]);
    }

    /** @test */
    public function should_process_line_create_device_network()
    {
        $network = (new NetworkBuilder())->build();
        $device  = (new DeviceBuilder())->build();
        $user    = (new UserBuilder())->withNetwork($network)->build();
        $sku     = '1231263';
        Auth::setUser($user);

        $line = [
            "deviceId" => $device->id,
            "networkId" => $network->id,
            "sku" => $sku,
        ];

        (new DevicesNetworkImportable())->processLine($line);

        $this->assertDatabaseHas('devices_network',
            [
                'deviceId' => $device->id,
                'networkId' => $network->id,
                'sku' => $sku
            ]
        );
    }

    /** @test */
    public function should_process_line_create_device_network_with_sku_being_device_id()
    {
        $device  = (new DeviceBuilder())->build();
        $network = (new NetworkBuilder())->build();
        $user    = (new UserBuilder())->withNetwork($network)->build();
        Auth::setUser($user);

        $line = [
            "deviceId" => "$device->id",
            "networkId" => "$network->id",
            "sku" => "",
        ];

        (new DevicesNetworkImportable())->processLine($line);

        $this->assertDatabaseHas('devices_network',
            [
                'deviceId' => $device->id,
                'networkId' => $network->id,
                'sku' => $device->id
            ]
        );
    }

    /** @test */
    public function should_process_line_throw_exception_when_user_not_belongs_to_network()
    {
        $class = new DevicesNetworkImportable();

        $device  = (new DeviceBuilder())->build();
        $network = (new NetworkBuilder())->build();
        $user    = (new UserBuilder())->build();

        Auth::setUser($user);

        $line = [
            "deviceId" => $device->id,
            "networkId" => $network->id,
            "sku" => '123'
        ];

        $this->expectExceptionMessage(trans('exceptions.' . ImportableExceptions::USER_CANNOT_ADD_TO_NETWORK));

        $class->processLine($line);
    }

    /** @test */
    public function should_process_line_throw_exception_when_network_not_exists()
    {
        $class = new DevicesNetworkImportable();

        $network = (new NetworkBuilder())->withSlug('tradeup-group')->build();
        $device  = (new DeviceBuilder())->build();
        $user    = (new UserBuilder())->withNetwork($network)->build();

        Auth::setUser($user);

        $line = [
            "deviceId" => $device->id,
            "networkId" => $network->id + 123,
            "sku" => '123'
        ];

        $this->expectExceptionMessage(trans('validation.exists', ['attribute' => 'identificador da rede']));

        $class->processLine($line);
    }

    /** @test */
    public function should_process_line_throw_exception_when_device_not_exists()
    {
        $class = new DevicesNetworkImportable();

        $network = (new NetworkBuilder())->withSlug('tradeup-group')->build();
        $device  = (new DeviceBuilder())->build();
        $user    = (new UserBuilder())->withNetwork($network)->build();

        Auth::setUser($user);

        $line = [
            "deviceId" => $device->id + 123,
            "networkId" => $network->id,
            "sku" => '123'
        ];

        $this->expectExceptionMessage(trans('validation.exists', ['attribute' => 'identificador do dispositivo']));

        $class->processLine($line);
    }

    /** @test */
    public function should_process_line_throw_exception_when_register_already_exists()
    {
        $class = new DevicesNetworkImportable();

        $network = (new NetworkBuilder())->build();
        $device  = (new DeviceBuilder())->withNetwork($network)->build();
        $user    = (new UserBuilder())->withNetwork($network)->build();

        Auth::setUser($user);

        $line = [
            "deviceId" => $device->id,
            "networkId" => $network->id,
            "sku" => '123'
        ];

        $this->expectExceptionMessage(trans('exceptions.' . ImportableExceptions::REGISTER_ALREADY_EXISTS));

        $class->processLine($line);
    }

    /** @test */
    public function should_process_line_throw_exception_when_device_id_is_invalid()
    {
        $line = [
            "deviceId" => 'abc',
            "sku" => '123',
            "networkId" => 2
        ];

        $this->expectException(\InvalidArgumentException::class);

        (new DevicesNetworkImportable())->processLine($line);
    }

    /** @test */
    public function should_process_line_throw_exception_when_network_id_is_invalid()
    {
        $line = [
            "deviceId" => 3,
            "sku" => '123',
            "networkId" => 'd'
        ];

        $this->expectException(\InvalidArgumentException::class);

        (new DevicesNetworkImportable())->processLine($line);
    }

}
