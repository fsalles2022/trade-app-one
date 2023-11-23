<?php

namespace Authorization\tests\Helpers\Builders;

use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;

class ThirdPartyConfigBuilder
{
    private $accessKey;
    private $accessUser;
    private $routes;
    private $whiteList;


    public function withAccessKey(string $accessKey): ThirdPartyConfigBuilder
    {
        $this->accessKey = $accessKey;
        return $this;
    }

    public function withAccessUser(User $user): ThirdPartyConfigBuilder
    {
        $this->accessUser = $user;
        return $this;
    }

    public function withWhiteList(string $whiteList): ThirdPartyConfigBuilder
    {
        $this->whiteList = $whiteList;
        return $this;
    }

    public function withRoutes(array $routes): ThirdPartyConfigBuilder
    {
        $this->routes = $routes;
        return $this;
    }

    public function build()
    {
        $user = $this->accessUser ?? (new UserBuilder())->build();
        return [
            "accessKey" => $this->accessKey,
            "accessUser" => $user->cpf,
            "accessWhiteList" => $this->whiteList ?? "127.0.0.1",
            "routes" => $this->routes ?? [
                "sale" => [
                    0 => "GET"
                ],
                "me" => [
                    0 => "GET"
                ]
            ]
        ];
    }
}
