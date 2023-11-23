<?php

namespace TradeAppOne\Tests\Feature;

use Buyback\Tests\Helpers\Builders\DeviceBuilder;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Models\Tables\Device;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class DeviceFeatureTest extends TestCase
{
    use AuthHelper;

    const COLLECTION_DEVICES = '/collection/devices';

    /** @test */
    public function get_should_return_devices_paginated(): void
    {
        (new DeviceBuilder())->build();

        $user = (new UserBuilder)->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get(self::COLLECTION_DEVICES);

        $response->assertJsonStructure(['current_page', 'data' => ['*' => ['id', 'label', 'model', 'brand', 'color', 'storage', 'type']]]);
    }

    /** @test */
    public function get_should_return_device_types(): void
    {
        factory(Device::class)->create();

        $this->authAs()
            ->get('/devices/types')
            ->assertJsonStructure([
            'types'=>[
                '*'=>[
                    'id',
                    'label'
                ]
            ]
        ])->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_return_200_filtering_devices(): void
    {
        $response = $this->authAs((new UserBuilder())->build())->post('/devices/by-types', ['type'=>'TABLET']);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function get_should_return_200_device_filtered_by_type(){
        $device = (new DeviceBuilder())->build();
        (new DeviceBuilder())->withType($device->type)->build();
        (new DeviceBuilder())->withType('NO_TYPE')->build();

        $response = $this->authAs((new UserBuilder())->build())->post('/devices/by-types', ['type'=>$device->type]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(2);
    }

    /** @test */
    public function get_should_return_no_device_when_filtering_by_type(): void
    {
        (new DeviceBuilder())->generateDeviceTimes(10);

        $response = $this->authAs((new UserBuilder())->build())->post('/devices/by-types', ['type'=>'NO_TYPE']);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0);
    }
}