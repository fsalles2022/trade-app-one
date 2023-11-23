<?php

namespace TradeAppOne\Tests\Feature;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Domain\Enumerators\Importables;
use TradeAppOne\Domain\Enumerators\Permissions\ImportablePermission;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Facades\SyncUserOperators;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserImportableBuilder;
use TradeAppOne\Tests\TestCase;

class UserImportFeatureTest extends TestCase
{
    use AuthHelper;

    protected $endpointPrefix = '/users/import';

    protected function setUp()
    {
        parent::setUp();
        Storage::fake('s3');
        SyncUserOperators::shouldReceive('sync')->atLeast();


        $dateFromFixture = Carbon::create(2019, 3, 19);
        Carbon::setTestNow($dateFromFixture);
    }

    /** @test */
    public function should_return_200_with_import_model(): void
    {
        $user = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get($this->endpointPrefix);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_return_422_when_file_not_provided(): void
    {
        $user = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->postJson($this->endpointPrefix);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function should_return_422_when_invalid_file_provided(): void
    {
        $permission        = ImportablePermission::getFullName(ImportablePermission::USER);
        $user              = (new UserBuilder())->withPermission($permission)->build();
        $userImportableCsv = (new UserImportableBuilder())->buildInvalidFile();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->postJson($this->endpointPrefix, [
                'file' => $userImportableCsv,
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function should_return_201_when_import_new_user_success(): void
    {
        $permission  = ImportablePermission::getFullName(ImportablePermission::USER);
        $pointOfSale = (new PointOfSaleBuilder())->build();
        $user        = (new UserBuilder())->withPointOfSale($pointOfSale)->withPermission($permission)->build();
        $hierarchy   = (new HierarchyBuilder())->withUser($user)->withNetwork($pointOfSale->network)->build();
        $role        = (new RoleBuilder())->withNetwork($pointOfSale->network)->build();
        $stub        = [
            [
                'nome' => 'Joao',
                'sobrenome' => 'pedro',
                'email' => 'joao@email.com',
                'cpf' => '20040370089',
                'dataDeNascimento' => '22/01/1997',
                'funcao' => $role->slug,
                'pontoDeVenda' => $pointOfSale->cnpj,
                'regional' => $hierarchy->slug
            ]
        ];

        $userImportableCsv = (new UserImportableBuilder())->buildFromArray($stub);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->postJson($this->endpointPrefix, [
                'file' => $userImportableCsv
            ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /** @test */
    public function should_registry_in_history_when_import_occur_errors(): void
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();
        $role        = (new RoleBuilder())->withNetwork($pointOfSale->network)->build();

        $permission = ImportablePermission::getFullName(ImportablePermission::USER);
        $user       = (new UserBuilder())->withPointOfSale($pointOfSale)->withPermission($permission)->build();
        $hierarchy  = (new HierarchyBuilder())->withUser($user)->withNetwork($pointOfSale->network)->build();

        $stub = [
            [
                'nome' => 'Joao',
                'sobrenome' => 'pedro',
                'email' => 'joao@email.com',
                'cpf' => '2004037008',
                'dataDeNascimento' => '22/01/1997',
                'funcao' => $role->slug,
                'pontoDeVenda' => $pointOfSale->cnpj,
                'regional' => $hierarchy->slug
            ]
        ];

        $userImportableCsv = (new UserImportableBuilder())->buildFromArray($stub);

        $this->withHeader('Authorization', $this->loginUser($user))
            ->postJson($this->endpointPrefix, [
                'file' => $userImportableCsv
            ]);

        $this->assertDatabaseHas('importHistory', ['status' => "ERROR", "userId" => $user->id]);
    }

    /** @test */
    public function should_return_201_when_update_user(): void
    {
        $pointOfSale = (new PointOfSaleBuilder())->build();
        $actualUser  = (new UserBuilder())->withPointOfSale($pointOfSale)->build();
        $newRole     = (new RoleBuilder())->withNetwork($pointOfSale->network)->build();

        $permission = ImportablePermission::getFullName(ImportablePermission::USER);
        $user       = (new UserBuilder())->withPointOfSale($pointOfSale)->withPermission($permission)->build();
        $hierarchy  = (new HierarchyBuilder())->withUser($user)->withNetwork($pointOfSale->network)->build();

        $stub = [
            [
                'nome' => 'Joao',
                'sobrenome' => 'pedro',
                'email' => 'joao@email.com',
                'cpf' => $actualUser->cpf,
                'dataDeNascimento' => '22/01/1997',
                'funcao' => $newRole->slug,
                'pontoDeVenda' => $pointOfSale->cnpj,
                'regional' => $hierarchy->slug
            ]
        ];

        $userImportableCsv = (new UserImportableBuilder())->buildFromArray($stub);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->postJson($this->endpointPrefix, [
                'file' => $userImportableCsv
            ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /** @test */
    public function should_return_line_error_when_import_no_pointOfSale_and_hierarchy(): void
    {
        $permission  = ImportablePermission::getFullName(ImportablePermission::USER);
        $pointOfSale = (new PointOfSaleBuilder())->build();
        $user        = (new UserBuilder())->withPointOfSale($pointOfSale)->withPermission($permission)->build();
        $role        = (new RoleBuilder())->withNetwork($pointOfSale->network)->build();
        $stub        = [
            [
                'nome' => 'Joao',
                'sobrenome' => 'pedro',
                'email' => 'joao@email.com',
                'cpf' => '20040370089',
                'dataDeNascimento' => '22/01/1997',
                'funcao' => $role->slug,
                'pontoDeVenda' => '',
                'regional' => ''
            ]
        ];

        $userImportableCsv = (new UserImportableBuilder())->buildFromArray($stub);

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->postJson($this->endpointPrefix, [
                'file' => $userImportableCsv
            ]);

        $response->assertSee(trans('exceptions.userImportable.noHierarchyAndPdv'));
    }

    /** @test */
    public function post_should_delete_user(): void
    {
        $user = (new UserBuilder())
            ->withPermission(ImportablePermission::getFullName(Importables::USERS_DELETE))
            ->build();

        $userToDelete = (new UserBuilder())->build();
        $stringCsv    = "cpf\n".$userToDelete->cpf;
        $fileName     = "user_delete.csv";

        Storage::fake('files');
        Storage::disk('files')->put($fileName, $stringCsv);

        $path = Storage::disk('files')->path($fileName);
        $csv  = (new UploadedFile($path, $fileName, null, null, null, true));

        $response = $this->authAs($user)->post('users/import-delete', ['file' => $csv]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertEquals(trans('messages.default_success'), $response->json('message'));

        $userDeleted = User::find($userToDelete->id);
        $this->assertEquals(UserStatus::INACTIVE, $userDeleted->activationStatusCode);
    }
}
