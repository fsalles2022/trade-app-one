<?php

namespace Core\HandBooks\Tests\Feature;

use Illuminate\Http\Response;
use TradeAppOne\Tests\TestCase;
use TradeAppOne\Tests\Helpers\AuthHelper;
use Core\HandBooks\Tests\Helpers\HandbookBuilder;
use Core\HandBooks\Enumerators\HandbookPermissions;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;

class HandbookFeatureTest extends TestCase
{
    use AuthHelper;

    public static function routeDelete(int $id)
    {
        return "handbooks/$id";
    }

    /** @test */
    public function delete_should_return_403_when_has_not_permission_delete()
    {
        $handbook = (new HandbookBuilder())->build();

        $response = $this->authAs($handbook->user)->delete(self::routeDelete($handbook->id));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(['shortMessage' => UserExceptions::UNAUTHORIZED]);
    }

    /** @test */
    public function delete_should_return_200_when_deleted_handbook()
    {
        $permission = HandbookPermissions::getFullName(HandbookPermissions::DELETE);
        $user       = (new UserBuilder())->withPermission($permission)->build();
        $handbook   = (new HandbookBuilder())->withUser($user)->build();

        $response = $this->authAs($handbook->user)->delete(self::routeDelete($handbook->id));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(trans('handbook::messages.deleted_success'));
        $this->assertTrue(filled($handbook->refresh()->createdAt));
    }
}
