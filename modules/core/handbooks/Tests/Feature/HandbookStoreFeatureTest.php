<?php

namespace Core\HandBooks\Tests\Feature;

use TradeAppOne\Facades\S3;
use Illuminate\Http\Response;
use TradeAppOne\Facades\Uniqid;
use TradeAppOne\Tests\TestCase;
use Core\HandBooks\Models\Handbook;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\FilterModes;
use Core\HandBooks\Services\HandbookFileService;
use Core\HandBooks\Exceptions\HandbookExceptions;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use Core\HandBooks\Enumerators\HandbookPermissions;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Domain\Enumerators\Files\FileExtensions;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use Core\HandBooks\Tests\Helpers\File\HandbookFileTestHelper;

class HandbookStoreFeatureTest extends TestCase
{
    use AuthHelper;

    public const URL = '/handbooks/';

    /** @test */
    public function post_return_403_when_has_not_permission_create()
    {
        $payload = [
            'title' => 'Title',
            'file' => HandbookFileTestHelper::file(),
            'module' => Operations::SECURITY,
            'category' => 'Teste',
            'networksFilterMode' => FilterModes::ALL,
            'rolesFilterMode' => FilterModes::ALL
        ];

        $response = $this->authAs()->post(self::URL, $payload);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(['shortMessage' => UserExceptions::UNAUTHORIZED]);
    }

    /** @test */
    public function post_return_404_when_module_not_exists()
    {
        $permission = HandbookPermissions::getFullName(HandbookPermissions::CREATE);
        $user       = (new UserBuilder())->withPermission($permission)->build();

        $payload = [
            'title' => 'Title',
            'file' => HandbookFileTestHelper::file(),
            'module' => 'MODULE FAKE',
            'category' => 'Teste',
            'networksFilterMode' => FilterModes::ALL,
            'rolesFilterMode' => FilterModes::ALL
        ];

        $response = $this->authAs($user)->post(self::URL, $payload);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJsonFragment(['shortMessage' => HandbookExceptions::OPERATION_NOT_FOUND]);
    }

    /** @test */
    public function post_return_200_when_filters_mode_is_all_and_save_success()
    {
        $permission = HandbookPermissions::getFullName(HandbookPermissions::CREATE);
        $user       = (new UserBuilder())->withPermission($permission)->build();

        $titleUniq = Uniqid::generate(); //Gero um titulo unico para dar assert no DB.
        Uniqid::shouldReceive('generate')->andReturn('123'); //Mock para poder gerar o mesmo expect Path

        $payload = [
            'title' => $titleUniq,
            'file' => HandbookFileTestHelper::file(),
            'module' => Operations::SECURITY,
            'category' => 'Teste',
            'networksFilterMode' => FilterModes::ALL,
            'rolesFilterMode' => FilterModes::ALL
        ];

        S3::shouldReceive('put')->once()->andReturnTrue(); //Mock a chamada ao S3, deve chamar ao menos uma vez.

        $expectedPath = HandbookFileService::generatePath(Operations::SECURITY, $titleUniq, FileExtensions::PDF);

        $response = $this->authAs($user)->post(self::URL, $payload);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment(trans('handbook::messages.created_success'));

        $this->assertDatabaseHas('handbooks', [
            'title' => $titleUniq,
            'file' => $expectedPath,
            'module' => Operations::SECURITY,
            'category' => 'Teste',
            'networksFilterMode' => FilterModes::ALL,
            'rolesFilterMode' => FilterModes::ALL
        ]);
    }

    /** @test */
    public function post_return_200_and_attach_correct_networks_and_roles()
    {
        $permission = HandbookPermissions::getFullName(HandbookPermissions::CREATE);
        $user       = (new UserBuilder())->withPermission($permission)->build();
        $titleUniq  = Uniqid::generate(); //Titulo unico para dar assert no DB.

        $payload = [
            'title' => $titleUniq,
            'file' => HandbookFileTestHelper::file(),
            'module' => Operations::TELECOMMUNICATION,
            'category' => 'Teste',
            'networksFilterMode' => FilterModes::CHOSEN,
            'rolesFilterMode' => FilterModes::CHOSEN,
            'networks' => [$user->getNetwork()->slug],
            'roles' => [$user->role->slug]
        ];

        S3::shouldReceive('put')->once()->andReturnTrue(); //Mock a chamada ao S3, deve chamar ao menos uma vez.

        $this->authAs($user)->post(self::URL, $payload);

        //Asserts
        $handbook = Handbook::query()->where('title', '=', $titleUniq)->first();

        $this->assertDatabaseHas('handbooks_networks', [ //Confirma se o relacionamento foi realizado.
            'handbookId' => $handbook->id,
            'networkId' => $user->getNetwork()->id
        ]);

        $this->assertDatabaseHas('handbooks_roles', [ //Confirma se o relacionamento foi realizado.
            'handbookId' => $handbook->id,
            'roleId' => $user->role->id
        ]);
    }

    /** @test */
    public function post_return_403_when_has_not_authorization_under_network()
    {
        $permission = HandbookPermissions::getFullName(HandbookPermissions::CREATE);
        $user       = (new UserBuilder())->withPermission($permission)->build();

        $network = factory(Network::class)->create();

        $payload = [
            'title' => 'Title',
            'file' => HandbookFileTestHelper::file(),
            'module' => Operations::SECURITY,
            'category' => 'Teste',
            'networksFilterMode' => FilterModes::CHOSEN,
            'rolesFilterMode' => FilterModes::ALL,
            'networks' => [$network->slug]
        ];

        $response = $this->authAs($user)->post(self::URL, $payload);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(['shortMessage' => UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_NETWORK]);
    }

    /** @test */
    public function post_return_403_when_has_not_authorization_under_roles()
    {
        $roleAdmin = (new RoleBuilder())->build();
        $role1     = (new RoleBuilder())->withParent($roleAdmin)->build();

        $permission = HandbookPermissions::getFullName(HandbookPermissions::CREATE);
        $user       = (new UserBuilder())->withRole($role1)->withPermission($permission)->build();

        $payload = [
            'title' => 'Title',
            'file' => HandbookFileTestHelper::file(),
            'module' => Operations::SECURITY,
            'category' => 'Teste',
            'networksFilterMode' => FilterModes::ALL,
            'rolesFilterMode' => FilterModes::CHOSEN,
            'roles' => [$roleAdmin->slug]
        ];

        $response = $this->authAs($user)->post(self::URL, $payload);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(['shortMessage' => UserExceptions::NOT_PERMISSION_UNDER_ROLE]);
    }
}
