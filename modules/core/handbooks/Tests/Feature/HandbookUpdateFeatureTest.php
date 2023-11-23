<?php

namespace Core\HandBooks\Tests\Feature;

use TradeAppOne\Facades\S3;
use Illuminate\Http\Response;
use TradeAppOne\Facades\Uniqid;
use TradeAppOne\Tests\TestCase;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Domain\Enumerators\FilterModes;
use Core\HandBooks\Services\HandbookFileService;
use Core\HandBooks\Tests\Helpers\HandbookBuilder;
use Core\HandBooks\Exceptions\HandbookExceptions;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use Core\HandBooks\Enumerators\HandbookPermissions;
use TradeAppOne\Domain\Enumerators\Files\FileExtensions;
use Core\HandBooks\Tests\Helpers\File\HandbookFileTestHelper;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;

class HandbookUpdateFeatureTest extends TestCase
{
    use AuthHelper;

    public static function url(int $id): string
    {
        return "/handbooks/edit/$id";
    }

    /** @test */
    public function post_return_404_when_not_exists_handbook_to_edit()
    {
        $response = $this->authAs()->post(self::url(99999), []);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJsonFragment(['shortMessage' => HandbookExceptions::NOT_FOUND]);
    }

    /** @test */
    public function post_return_403_when_has_not_permission_edit()
    {
        $handbook = (new HandbookBuilder())->build();

        $response = $this->authAs($handbook->user)->post(self::url($handbook->id), []);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(['shortMessage' => UserExceptions::UNAUTHORIZED]);
    }

    /** @test */
    public function post_return_200_and_not_call_s3_when_not_has_file()
    {
        $permission = HandbookPermissions::getFullName(HandbookPermissions::EDIT);
        $user       = (new UserBuilder())->withPermission($permission)->build();
        $handbook   = (new HandbookBuilder())->withUser($user)->build();

        S3::shouldReceive('delete')->never();
        S3::shouldReceive('put')->never();

        $response = $this->authAs($handbook->user)->post(self::url($handbook->id), []);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(trans('handbook::messages.updated_success'));
    }

    /** @test */
    public function post_return_200_and_alter_attributes()
    {
        $permission = HandbookPermissions::getFullName(HandbookPermissions::EDIT);
        $user       = (new UserBuilder())->withPermission($permission)->build();
        $handbook   = (new HandbookBuilder())->withUser($user)->build();

        $this->assertDatabaseMissing('handbooks_roles', ['roleId' => $user->role->id, 'handbookId' => $handbook->id]);

        $payload = [
            'title' => 'Title-altered',
            'description' => 'Description-altered',
            'networksFilterMode' => FilterModes::CHOSEN,
            'rolesFilterMode' => FilterModes::CHOSEN,
            'roles' => [$user->role->slug],
            'networks' => [$user->getNetwork()->slug]
        ];

        $response = $this->authAs($handbook->user)->post(self::url($handbook->id), $payload);

        unset($payload['roles'], $payload['networks']);

        $this->assertDatabaseHas('handbooks', $payload);
        $this->assertDatabaseHas('handbooks_roles', ['roleId' => $user->role->id, 'handbookId' => $handbook->id]);

        $response->assertJsonFragment(trans('handbook::messages.updated_success'));
    }

    /** @test */
    public function post_return_200_and_alter_file()
    {
        $permission = HandbookPermissions::getFullName(HandbookPermissions::EDIT);
        $user       = (new UserBuilder())->withPermission($permission)->build();
        $handbook   = (new HandbookBuilder())->withUser($user)->build();

        $payload = [
          'file' => HandbookFileTestHelper::file()
        ];

        S3::shouldReceive('delete')->once();
        S3::shouldReceive('put')->once();
        Uniqid::shouldReceive('generate')->andReturn('123'); //Mock para poder gerar o mesmo expect Path

        $expectedPath = HandbookFileService::generatePath($handbook->module, $handbook->title, FileExtensions::PDF);

        $response = $this->authAs($handbook->user)->post(self::url($handbook->id), $payload);

        $this->assertDatabaseHas('handbooks', ['id' => $handbook->id, 'file' => $expectedPath]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(trans('handbook::messages.updated_success'));
    }
}
