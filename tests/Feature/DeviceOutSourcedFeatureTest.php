<?php

namespace TradeAppOne\Tests\Feature;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Importables\DeviceOutSourcedImportable;
use TradeAppOne\Exceptions\ImportableExceptions;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\DeviceOutSourcedImportableBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class DeviceOutSourcedFeatureTest extends TestCase
{
    use AuthHelper;
    private $userHelper;

    protected function setUp()
    {
        parent::setUp();
        Storage::fake('s3');
        $this->userHelper = (new UserBuilder())->build();
    }

    /** @test */
    public function should_return_response_with_status_200_when_call_import_model()
    {
        $response = $this->authAs($this->userHelper)
            ->get('devices-outsourced/import');
        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_return_response_with_correct_content_when_call_import_model()
    {
        $deviceOutSourcedImportable = new DeviceOutSourcedImportable();
        $columns = array_values($deviceOutSourcedImportable->getColumns());
        $networkSlug = $this->userHelper->getNetwork()->slug;
        $lines = $deviceOutSourcedImportable->getExample($networkSlug);

        $csv = CsvHelper::arrayToCsv([$columns, $lines]);

        $response = $this->authAs($this->userHelper)
            ->get('devices-outsourced/import');

        $response->assertSee($csv->getContent());
    }

    /** @test */
    public function should_return_response_with_status_201_when_call_import_with_correct_parameters()
    {
        $file = (new DeviceOutSourcedImportableBuilder())->withNetwork($this->userHelper->getNetwork())->build();
        $response = $this->authAs($this->userHelper)
            ->post('devices-outsourced/import', ['file' => $file]);
        $response->assertStatus(Response::HTTP_CREATED);
    }

    /** @test */
    public function should_return_correct_message_when_call_import_with_correct_parameters()
    {
        $file = (new DeviceOutSourcedImportableBuilder())->withNetwork($this->userHelper->getNetwork())->build();
        $response = $this->authAs($this->userHelper)
            ->post('devices-outsourced/import', ['file' => $file]);
        $response->assertExactJson(['message' => trans('messages.default_success')]);
    }

    /** @test */
    public function should_return_error_message_when_call_import_and_user_cannot_add_to_network()
    {
        $file = (new DeviceOutSourcedImportableBuilder())->build();
        $response = $this->authAs($this->userHelper)
            ->post('devices-outsourced/import', ['file' => $file]);
        $response->assertSee(trans('exceptions.' . ImportableExceptions::USER_CANNOT_ADD_TO_NETWORK));
    }
}
