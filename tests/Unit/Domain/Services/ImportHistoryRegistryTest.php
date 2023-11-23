<?php

namespace TradeAppOne\Tests\Unit\Domain\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Enumerators\ImportHistoryStatus;
use TradeAppOne\Domain\Importables\ImportablePath;
use TradeAppOne\Domain\Services\ImportHistoryRegistry;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ImportHistoryRegistryTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Storage::fake('s3');

        $dateFromFixture = Carbon::create(2019, 3, 19);
        Carbon::setTestNow($dateFromFixture);
    }

    /** @test */
    public function should_save_input_file_pending_with_file()
    {
        $user = (new UserBuilder())->build();
        $file = UploadedFile::fake()->create('teste.csv');

        $successPath = ImportablePath::generate($user, Importables::USERS, 'INPUT', 1);
        $s3Path      = Storage::disk('s3')->url($successPath);

        (new ImportHistoryRegistry())
            ->user($user)
            ->type(Importables::USERS)
            ->savePendingFile($file);

        $this->assertDatabaseHas('importHistory',
            [
                "type" => Importables::USERS,
                "inputFile" => $s3Path,
                "outputFile" => null,
                'status' => ImportHistoryStatus::PENDING,
                "userId" => $user->id,
            ]
        );

        Storage::disk('s3')->assertExists($successPath);
    }

    /** @test */
    public function should_save_error_file_persist_in_s3_and_table()
    {
        $user = (new UserBuilder())->build();
        $file = UploadedFile::fake()->create('teste.csv');

        $successPath = ImportablePath::generate($user, Importables::USERS, 'INPUT', 1);
        $s3Path      = Storage::disk('s3')->url($successPath);

        $importHistoy = (new ImportHistoryRegistry())
            ->user($user)
            ->type(Importables::USERS)
            ->savePendingFile($file);

        $errorFile   = UploadedFile::fake()->create('teste-error.csv');
        $erroPath    = ImportablePath::generate($user, Importables::USERS, 'OUTPUT', 1);
        $s3ErrorPath = Storage::disk('s3')->url($erroPath);

        $importHistoy->saveErrorFile($errorFile);

        $this->assertDatabaseHas('importHistory',
            [
                "type" => Importables::USERS,
                "inputFile" => $s3Path,
                "outputFile" => $s3ErrorPath,
                'status' => ImportHistoryStatus::ERROR,
                "userId" => $user->id,
            ]
        );

        Storage::disk('s3')->assertExists($successPath);
        Storage::disk('s3')->assertExists($erroPath);
    }

    /** @test */
    public function should_success_persist_in_s3_and_table()
    {
        $user = (new UserBuilder())->build();
        $file = UploadedFile::fake()->create('teste.csv');

        $successPath = ImportablePath::generate($user, Importables::USERS, 'INPUT', 1);
        $s3Path      = Storage::disk('s3')->url($successPath);

        $errorFile   = UploadedFile::fake()->create('teste-error.csv');
        $erroPath    = ImportablePath::generate($user, Importables::USERS, 'OUTPUT', 1);
        $s3ErrorPath = Storage::disk('s3')->url($erroPath);

        $importHistoy = (new ImportHistoryRegistry())
            ->user($user)
            ->type(Importables::USERS)
            ->savePendingFile($file);

        $importHistoy->saveErrorFile($errorFile);

        $importHistoy->success();

        $this->assertDatabaseHas('importHistory',
            [
                "type" => Importables::USERS,
                "inputFile" => $s3Path,
                "outputFile" => $s3ErrorPath,
                'status' => ImportHistoryStatus::SUCCESS,
                "userId" => $user->id,
            ]
        );

        Storage::disk('s3')->assertExists($successPath);
        Storage::disk('s3')->assertExists($erroPath);
    }

}