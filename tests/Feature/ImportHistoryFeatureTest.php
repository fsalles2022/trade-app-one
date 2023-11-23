<?php

namespace TradeAppOne\Tests\Feature;

use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Domain\Components\Helpers\FilePathFromUrl;
use TradeAppOne\Domain\Enumerators\ImportHistoryStatus;
use TradeAppOne\Domain\Models\Tables\ImportHistory;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ImportHistoryFeatureTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function should_return_200_with_importHistory()
    {

        $user = (new UserBuilder())->build();
        factory(ImportHistory::class)->create([
            'userId' => $user->id
        ]);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get('users/import-history');

        $response->assertJsonCount(1, 'data');
        $response->assertJsonStructure([
           'data' => [ '*' => [
               'id',
               'type',
               'inputFile',
               'status',
               'user' => [
                   'id',
                   'firstName',
                   'lastName',
                   'email',
                   'cpf',
                   'areaCode'
               ]
           ]]
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_return_200_with_importHistory_from_own_user()
    {
        $master  = (new RoleBuilder())->build();
        $blaster = (new RoleBuilder())->withParent($master)->build();

        $user = (new UserBuilder())->withRole($blaster)->build();
        factory(ImportHistory::class)->create([
            'userId' => $user->id
        ]);

        $this->authAs($user)
            ->get('users/import-history')
            ->assertJsonCount(1, 'data')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'inputFile',
                        'status',
                        'user' => [
                            'id',
                            'firstName',
                            'lastName',
                            'email',
                            'cpf',
                            'areaCode'
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function should_return_items_when_filter_by_cpf()
    {
        $blaster = (new RoleBuilder())->build();
        $master  = (new RoleBuilder())->withParent($blaster)->build();

        $user = (new UserBuilder())->withRole($blaster)->build();
        factory(ImportHistory::class, 3)->create([
            'userId' => $user->id
        ]);

        $userExample = (new UserBuilder())->withRole($master)->build();
        factory(ImportHistory::class, 2)->create([
            'userId' => $userExample->id
        ]);

        $this->authAs($user)
            ->get('users/import-history')
            ->assertJsonCount(5, 'data')
            ->assertStatus(Response::HTTP_OK);

        $this->authAs($userExample)
            ->get('users/import-history')
            ->assertJsonCount(2, 'data')
            ->assertStatus(Response::HTTP_OK);

        $this->authAs($user)
            ->get('users/import-history?' . "firstName=$userExample->firstName")
            ->assertJsonCount(2, 'data')
            ->assertStatus(Response::HTTP_OK);

        $this->authAs($userExample)
            ->get('users/import-history?' . "cpf=$userExample->cpf")
            ->assertJsonCount(2, 'data')
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_download_return_200()
    {
        $role = (new RoleBuilder())->build();
        $user = (new UserBuilder())->withRole($role)->build();

        $fileUpload = UploadedFile::fake()->create('import.csv');

        $importSuccess = factory(ImportHistory::class)->create([
            'userId' => $user->id,
            'status' => ImportHistoryStatus::SUCCESS
        ]);
        $importError = factory(ImportHistory::class)->create([
            'userId' => $user->id,
            'status' => ImportHistoryStatus::ERROR
        ]);

        $fileOutput = FilePathFromUrl::extractS3Path($importError->outputFile);
        $fileInput = FilePathFromUrl::extractS3Path($importSuccess->inputFile);
        Storage::shouldReceive('disk')->with('s3')->andReturnSelf();
        Storage::shouldReceive('download')->with($fileInput)->andReturn($fileUpload);
        Storage::shouldReceive('download')->with($fileOutput)->andReturn($fileUpload);
        Storage::shouldReceive('exists')->andReturn(true);

        $this->authAs($user)
            ->get('users/import-history/download/' . $importSuccess->id)
            ->assertStatus(Response::HTTP_OK);
        $this->authAs($user)
            ->get('users/import-history/download/' . $importError->id)
            ->assertStatus(Response::HTTP_OK);
    }
}
