<?php

namespace Core\HandBooks\Tests\Feature;

use Core\HandBooks\Exceptions\HandbookExceptions;
use Core\HandBooks\Models\Handbook;
use Core\HandBooks\Tests\Helpers\File\HandbookFileTestHelper;
use Core\HandBooks\Tests\Helpers\HandbookBuilder;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use TradeAppOne\Domain\Enumerators\Files\FileTypes;
use TradeAppOne\Domain\Enumerators\FilterModes;
use TradeAppOne\Exceptions\SystemExceptions\UserExceptions;
use TradeAppOne\Facades\S3;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class HandbookShowFeatureTest extends TestCase
{
    use AuthHelper;

    public static function edit(int $id): string
    {
        return "handbooks/$id/edit";
    }

    public static function show(int $id): string
    {
        return "handbooks/$id/show";
    }

    /** @test */
    public function get_should_return_correct_strutucture_to_edit()
    {
        $handbook = (new HandbookBuilder())->build();

        $response = $this->authAs($handbook->user)->get(self::edit($handbook->id));

        $response->assertJsonStructure([
            'id',
            'title',
            'file',
            'networksFilterMode',
            'rolesFilterMode',
            'module' => [
                'slug',
                'label',
            ],
            'category' => [
                'slug',
                'label'
            ],
            'networks',
            'roles'
        ]);
    }

    /** @test */
    public function get_should_return_403_when_has_not_permission_under_handbook_show_to_edit()
    {
        $roleAdmin = (new RoleBuilder())->build();
        $roleUser  = (new RoleBuilder())->withParent($roleAdmin)->build();

        $userAdmin = (new UserBuilder())->withRole($roleAdmin)->build();
        $user      = (new UserBuilder())->withRole($roleUser)->build();
        $handbook  = (new HandbookBuilder())->withUser($userAdmin)->build();

        $response = $this->authAs($user)->get(self::edit($handbook->id));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(['shortMessage' => UserExceptions::HAS_NOT_AUTHORIZATION_UNDER_USER]);
    }

    /** @test */
    public function get_should_return_link_when_file_type_is_video()
    {
        $handbook       = (new HandbookBuilder())->build();
        $handbook->type = FileTypes::VIDEO;
        $handbook->save();

        $link = 'https://tradeappone.com';
        S3::shouldReceive('url')->once()->andReturn($link);

        $response = $this->authAs($handbook->user)->get(self::show($handbook->id));
        $response->assertJsonFragment(['link' => $link]);
    }

    /** @test */
    public function get_should_return_document_when_file_type_is_document()
    {
        $handbook = (new HandbookBuilder())->build();

        $streamedResponse = new StreamedResponse(function () {
            return file_get_contents(HandbookFileTestHelper::file());
        });

        S3::shouldReceive('download')->once()->andReturn($streamedResponse);

        $response = $this->authAs($handbook->user)->get(self::show($handbook->id));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertTrue($response->isOk());
    }

    /** @test */
    public function get_should_return_403_when_has_not_permission_under_handbook_show()
    {
        $user     = (new UserBuilder())->build();
        $handbook = factory(Handbook::class)->make([
            'networksFilterMode' => FilterModes::CHOSEN,
            'rolesFilterMode' => FilterModes::CHOSEN
        ]);

        $handbook->user()->associate($user);
        $handbook->save();

        $response = $this->authAs()->get(self::show($handbook->id));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJsonFragment(['shortMessage' => HandbookExceptions::HAS_NOT_PERMISSION_UNDER_HANDBOOK]);
    }
}
