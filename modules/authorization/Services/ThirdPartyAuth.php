<?php

namespace Authorization\Services;

use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\UserService;
use Tymon\JWTAuth\Facades\JWTAuth;

class ThirdPartyAuth
{
    public function retrieveBearerToken($user): ?string
    {
        if ($user instanceof User) {
            $token = JWTAuth::fromUser($user);
            Auth::login($user);
            resolve(UserService::class)->makeActiveToken($token);
            return $token;
        }

        return null;
    }
}
