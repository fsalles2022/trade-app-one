<?php

namespace TradeAppOne\Tests\Feature;

use Buyback\Tests\Helpers\TradeInServices;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Models\Tables\Permission;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class BackOfficeFeatureTest extends TestCase
{
    use AuthHelper;

    const BACKOFFICE = '/sales/backoffice';

    /** @test */
    public function should_save_comment_backoffice_and_return_200(): void
    {
        $permission = factory(Permission::class)
            ->create([
                'slug' => SalePermission::getFullName(SalePermission::CREATE_BACKOFFICE)
            ]);

        $user = (new UserBuilder())->withPermissions([$permission])->build();

        $servicePrototype = TradeInServices::IplaceMobile();
        $sale             = (new SaleBuilder())->withServices([$servicePrototype])->build();
        $service          = $sale->services()->first();

        $payload = [
            'serviceTransaction' => $service->serviceTransaction,
            'comment' => 'TEST_COMMENT_BACKOFFICE'
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->post(self::BACKOFFICE, $payload);

        $assert = [
            'services.backoffice.comment'  => $payload['comment'],
            'services.backoffice.user.cpf' => $user->cpf
        ];

        $this->assertDatabaseHas('sales', $assert, 'mongodb');

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_exception_when_user_not_permission_backoffice(): void
    {
        $user = (new UserBuilder())->build();

        $servicePrototype = TradeInServices::IplaceMobile();
        $sale             = (new SaleBuilder())->withServices([$servicePrototype])->build();
        $service          = $sale->services()->first();

        $payload = [
            'serviceTransaction' => $service->serviceTransaction,
            'comment' => 'TEST_COMMENT_BACKOFFICE'
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->post(self::BACKOFFICE, $payload);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
