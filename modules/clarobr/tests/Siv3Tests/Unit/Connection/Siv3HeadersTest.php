<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\Unit\Connection;

use ClaroBR\Siv3Headers;
use ClaroBR\Tests\Siv3Tests\Siv3TestBook;
use TradeAppOne\Tests\TestCase;

class Siv3HeadersTest extends TestCase
{
    /** @return array[] */
    public function providerProperties(): array
    {
        return [
            [
                Siv3TestBook::DEFAULT_URI,
                'getUri',
            ],
            [
                [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
                'getHeaders',
            ],
        ];
    }

    /**
     * @dataProvider providerProperties
     * @param mixed $expected
     */
    public function test_should_get_equals_by_expected_and_method($expected, string $method): void
    {
        $headers = new Siv3Headers([
            'uri' => Siv3TestBook::DEFAULT_URI,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        ]);

        $this->assertEquals($expected, $headers->{$method}());
    }

    public function test_should_return_array_credentials()
    {
        $headers = new Siv3Headers([
            'login' => data_get(Siv3TestBook::AUTH_SIV3_CREDENTIALS, 'login'),
            'password' => data_get(Siv3TestBook::AUTH_SIV3_CREDENTIALS, 'password')
        ]);

        $this->assertArrayHasKey('email', $headers->getCredentials());
        $this->assertArrayHasKey('password', $headers->getCredentials());
    }
}
