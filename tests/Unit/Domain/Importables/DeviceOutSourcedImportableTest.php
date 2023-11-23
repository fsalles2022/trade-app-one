<?php

namespace TradeAppOne\Tests\Unit\Domain\Importables;

use InvalidArgumentException;
use TradeAppOne\Domain\Components\Helpers\ImportableHelper;
use TradeAppOne\Domain\Importables\DeviceOutSourcedImportable;
use TradeAppOne\Domain\Models\Tables\DeviceOutSourced;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class DeviceOutSourcedImportableTest extends TestCase
{
    private $userHelper;

    protected function setUp()
    {
        parent::setUp();
        $this->userHelper = (new UserBuilder())->build();
        $this->be($this->userHelper);
    }

    /** @test */
    public function should_return_a_device_out_sourced_instance_when_process_line()
    {
        $networkId = $this->userHelper->getNetwork()->slug;
        $deviceOutSourcedImportable = new DeviceOutSourcedImportable;

        $line = ImportableHelper::makeLine($deviceOutSourcedImportable, [$networkId]);
        $response = $deviceOutSourcedImportable->processLine($line);

        $this->assertInstanceOf(DeviceOutSourced::class, $response);
    }

    /** @test */
    public function should_persist_device_out_sourced_when_it_does_not_exist()
    {
        $network = $this->userHelper->getNetwork();
        $deviceOutSourcedImportable = new DeviceOutSourcedImportable;

        $line = ImportableHelper::makeLine($deviceOutSourcedImportable, [$network->slug]);
        $deviceOutSourcedImportable->processLine($line);
        unset($line['networkSlug']);
        $line['networkId'] = $network->id;
        $this->assertDatabaseHas('devices_outsourced', $line);
    }

    /** @test */
    public function should_update_device_out_sourced_when_it_exists()
    {
        $network = $this->userHelper->getNetwork();
        $deviceOutSourcedImportable = new DeviceOutSourcedImportable;

        $line = ImportableHelper::makeLine($deviceOutSourcedImportable, [$network->id]);
        $line['networkId'] = $network->id;
        DeviceOutSourced::query()->create($line);
        $line['networkSlug'] = $network->slug;
        $line['model'] = 'Galaxy Note 10';
        $deviceOutSourcedImportable->processLine($line);
        unset($line['networkSlug']);
        $this->assertDatabaseHas('devices_outsourced', $line);
    }

    /** @test */
    public function should_return_invalid_argument_exception_when_line_is_invalid()
    {
        $deviceOutSourcedImportable = new DeviceOutSourcedImportable;

        $this->expectException(InvalidArgumentException::class);
        $deviceOutSourcedImportable->processLine([]);
    }

    /** @test */
    public function should_return_exception_when_user_cannot_add_to_network()
    {
        $deviceOutSourcedImportable = new DeviceOutSourcedImportable;

        $line = ImportableHelper::makeLine($deviceOutSourcedImportable, [2]);
        $line['networkSlug'] = 'naoexiste';
        $this->expectExceptionMessage('O valor rede não existe.');
        $deviceOutSourcedImportable->processLine($line);
    }
}
