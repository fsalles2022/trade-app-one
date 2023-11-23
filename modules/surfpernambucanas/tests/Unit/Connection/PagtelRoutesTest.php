<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Connection;

use ReflectionClass;
use SurfPernambucanas\Connection\PagtelRoutes;
use TradeAppOne\Tests\TestCase;

class PagtelRoutesTest extends TestCase
{
    /** @return array[] */
    public function routes_provider(): array
    {
        $reflection = new ReflectionClass(PagtelRoutes::class);

        return [
            [
                $reflection,
                'AUTHENTICATE',
            ],
            [
                $reflection,
                'SUBSCRIBER_ACTIVATE',
            ],
            [
                $reflection,
                'ALLOCATED_MSISDN',
            ],
            [
                $reflection,
                'GET_VALUES',
            ],
            [
                $reflection,
                'GET_CARD',
            ],
            [
                $reflection,
                'ADD_CARD',
            ],
            [
                $reflection,
                'RECHARGE',
            ],
            [
                $reflection,
                'PLANS',
            ],
            [
                $reflection,
                'ACTIVATIONS',
            ],
        ];
    }

    /** @dataProvider routes_provider */
    public function test_should_has_constants_with_end_points_pagtel(ReflectionClass $reflection, string $route): void
    {
        $this->assertTrue(
            $this->is_route_valid($reflection, $route),
            "Route {$route} not defined or empty"
        );
    }

    private function is_route_valid(ReflectionClass $reflection, string $route): bool
    {
        return $reflection->hasConstant($route) && empty($reflection->getConstant($route)) === false;
    }
}
