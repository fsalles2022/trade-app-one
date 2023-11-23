<?php

namespace TradeAppOne\Tests\Helpers;

use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

trait AuthHelper
{
    public function loginUser(User $user): String
    {
        $token = JWTAuth::fromUser($user);
        return "Bearer {$token}";
    }

    public function authAs(?User $user = null): TestCase
    {
        $user = $user ?? (new UserBuilder())->build();
        $this->withHeader('Authorization', $this->loginUser($user));

        return $this;
    }

    public function loginWithAuthFacade(?User $user = null): User
    {
        $user = $user === null ? (new UserBuilder())->build() : $user;
        Auth::login($user);

        return $user;
    }
}
