<?php

namespace TradeAppOne\Tests\Unit\Domain\Services;

use Illuminate\Http\Response;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PasswordResetBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\RoleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class UserReaderServiceTest extends TestCase
{
    use AuthHelper;

    /** @test */
    public function should_export_users_with_filters()
    {
        $role        = (new RoleBuilder())->build();
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->build();
        $user        = (new UserBuilder())->withRole($role)->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        $otherUser   = (new UserBuilder())->withRole($role)->withNetwork($network)->withPointOfSale($pointOfSale)->build();
        (new PasswordResetBuilder())->withUser($user)->build();

        $responseFirstUser = $this->authAs($user)
            ->post('/users/export?' . 'cpf=' . $user->cpf)
            ->assertStatus(Response::HTTP_OK);

        $responseBothtUsers = $this->authAs($user)
            ->post('/users/export')
            ->assertStatus(Response::HTTP_OK);

        $this->assertContains($user->cpf, $responseFirstUser->content());
        $this->assertNotContains($otherUser->cpf, $responseFirstUser->content());

        $this->assertContains($otherUser->cpf, $responseBothtUsers->content());
    }
}
