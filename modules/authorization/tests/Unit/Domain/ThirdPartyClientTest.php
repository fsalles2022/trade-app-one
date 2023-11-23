<?php

namespace Authorization\tests\Unit\Domain;

use Authorization\Exceptions\RouteNotAvailableException;
use Authorization\Models\ThirdPartyClient;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class ThirdPartyClientTest extends TestCase
{
    /** @test */
    public function should_return_same_instance_when_wildcard_ip()
    {
        $user   = (new UserBuilder())->build();
        $result = (new ThirdPartyClient('', $user, ['*'], ['']));
        self::assertInstanceOf(ThirdPartyClient::class, $result->withIp('12'));
    }

    /** @test */
    public function should_return_exception_when_has_regex_route_and_no_match()
    {
        $this->expectException(RouteNotAvailableException::class);
        $user   = (new UserBuilder())->build();
        $result = (new ThirdPartyClient('', $user, ['*'], [0 => ['uri' => 'users', 'method' => 'POST']]));
        self::assertInstanceOf(ThirdPartyClient::class, $result->withRoute('users/86666666666'));
    }

    /** @test */
    public function should_return_success_when_has_regex_route_and_match()
    {
        $uriExample        = 'vouchers/:number:/available/:cpf:';
        $methodExample     = 'GET';
        $requestUrlExample = 'vouchers/50/available/00000009652';

        $user   = (new UserBuilder())->build();
        $result = (new ThirdPartyClient('', $user, ['*'], [0 => ['uri' => $uriExample, 'method' => $methodExample]]));
        self::assertInstanceOf(ThirdPartyClient::class, $result->withRoute($requestUrlExample, $methodExample));
    }
}
