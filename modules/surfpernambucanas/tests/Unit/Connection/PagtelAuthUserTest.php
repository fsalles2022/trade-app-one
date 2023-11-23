<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Connection;

use SurfPernambucanas\Connection\PagtelAuthUser;
use TradeAppOne\Tests\TestCase;

class PagtelAuthUserTest extends TestCase
{
    private const LOGIN      = 'aabbcc';
    private const PASSWORD   = '12345678';
    private const GRANT_TYPE = 'password';

    public function setUp(): void
    {
        parent::setUp();

        $this->set_config_mock();
    }

    /** @return array[] */
    public function provider_properties(): array
    {
        return [
            [
                self::LOGIN,
                'getLogin'
            ],
            [
                self::PASSWORD,
                'getPassword'
            ],
            [
                self::GRANT_TYPE,
                'getGrantType'
            ],
        ];
    }

    /** @dataProvider provider_properties */
    public function test_should_get_equals_by_expected_and_method(string $expected, string $method): void
    {
        $user = $this->resolve_pagtel_auth_user();

        $this->assertEquals(
            $expected,
            $user->$method()
        );
    }

    public function resolve_pagtel_auth_user(): PagtelAuthUser
    {
        return resolve(PagtelAuthUser::class);
    }

    public function set_config_mock(): void
    {
        $this->app->bind(PagtelAuthUser::class, function (): PagtelAuthUser {
            return new PagtelAuthUser([
                'login'      => self::LOGIN,
                'password'   => self::PASSWORD,
                'grant_type' => self::GRANT_TYPE,
            ]);
        });
    }
}
