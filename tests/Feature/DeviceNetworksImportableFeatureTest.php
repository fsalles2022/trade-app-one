<?php

namespace TradeAppOne\Tests\Feature;

use Buyback\Tests\Helpers\Builders\DeviceBuilder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\DeviceNetworksImportableBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class DeviceNetworksImportableFeatureTest extends TestCase
{
    use AuthHelper;
    private $endpointPrefix = 'devices-network/import';

    /** @test */
    public function should_return_200_with_import_model()
    {
        $user = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get($this->endpointPrefix);

        $expectedFile = base_path('tests/Helpers/Fixtures/devices_network_import_model.csv');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(file_get_contents($expectedFile), $response->getContent());
    }

    /** @test */
    public function should_return_422_when_file_not_provided()
    {
        $user = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->post($this->endpointPrefix);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function should_return_201_when_import_new_role()
    {
        Storage::fake('s3');
        $network = (new NetworkBuilder())->build();
        $user    = (new UserBuilder())->withNetwork($network)->build();
        $device  = (new DeviceBuilder())->build();

        $sku = '1231263';

        $line = [
            [
                "identificadorDoDispositivo" => $device->id,
                "sku" => $sku,
                "identificadorDaRede" => $network->id,
            ]
        ];

        $devicesImportableCsv = (new DeviceNetworksImportableBuilder())->buildFromArray($line);
        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->postJson($this->endpointPrefix, [
                'file' => $devicesImportableCsv
            ]);
        $response->assertStatus(Response::HTTP_CREATED);
    }

    /** @test */
    public function should_return_422_when_file_has_errors()
    {
        Storage::fake('s3');
        $network = (new NetworkBuilder())->build();
        $user    = (new UserBuilder())->withNetwork($network)->build();
        $device  = (new DeviceBuilder())->build();
        $sku     = '1231263';

        $line = [
            [
                "identificadorDoDispositivo" => $device->id,
                "sku" => $sku,
                "identificadorDaRede" => '182323',
            ]
        ];

        $devicesImportableCsv = (new DeviceNetworksImportableBuilder())->buildFromArray($line);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->postJson($this->endpointPrefix, [
                'file' => $devicesImportableCsv
            ]);

        $response->assertStatus(Response::HTTP_OK);
    }
}