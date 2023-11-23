<?php

namespace Authorization\tests\Helpers\Builders;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;

class ThirdPartyDatabaseBuilder
{
    private $accessKey;
    private $accessUser;
    private $routes;
    private $whiteList;


    public function withAccessKey(string $accessKey): ThirdPartyDatabaseBuilder
    {
        $this->accessKey = $accessKey;
        return $this;
    }

    public function withAccessUser(User $user): ThirdPartyDatabaseBuilder
    {
        $this->accessUser = $user;
        return $this;
    }

    public function withWhiteList(string $whiteList): ThirdPartyDatabaseBuilder
    {
        $this->whiteList = $whiteList;
        return $this;
    }

    public function withRoutes(Collection $routes): ThirdPartyDatabaseBuilder
    {
        $this->routes = $routes;
        return $this;
    }

    public function build()
    {
        $user = $this->accessUser ?? (new UserBuilder())->build();
        return [
            "accessKey" => $this->accessKey,
            "user" => $user,
            "whitelist" => collect([0 => ['ip' => $this->whiteList ?? '127.0.0.1']]),
            "routes" => $this->routes ?? collect([
                [
                    'uri' => 'sale',
                    'method' => 'GET'
                ],
                [
                    'uri' => 'me',
                    'method' => 'GET'
                ],
            ])
        ];
    }
}
