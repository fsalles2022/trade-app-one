<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Connection;

use SurfPernambucanas\Connection\PagtelHeaders;
use TradeAppOne\Tests\TestCase;

class PagtelHeadersTest extends TestCase
{
    private const URI     = 'https://endpoint.test';
    private const HEADERS = [
        'Content-Type' => 'application/json',
        'Accept'       => 'application/json',
    ];

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
                self::URI,
                'getUri',
            ],
            [
                self::HEADERS,
                'getHeaders',
            ],
        ];
    }

    /**
     * @dataProvider provider_properties
     * @param mixed $expected
     */
    public function test_should_get_equals_by_expected_and_method($expected, string $method): void
    {
        $headers = $this->resolve_pagtel_headers();

        $this->assertEquals(
            $expected,
            $headers->$method()
        );
    }

    public function resolve_pagtel_headers(): PagtelHeaders
    {
        return resolve(PagtelHeaders::class);
    }

    public function set_config_mock(): void
    {
        $this->app->bind(PagtelHeaders::class, function (): PagtelHeaders {
            return new PagtelHeaders([
                'uri'         => self::URI,
                'headers'     => self::HEADERS
            ]);
        });
    }
}
