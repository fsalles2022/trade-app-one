<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\Unit\Connection;

use ClaroBR\Connection\Siv3Routes;
use TradeAppOne\Tests\TestCase;
use ReflectionClass;

class Siv3RoutesTest extends TestCase
{
    /** @return array[] */
    public function routes_provider(): array
    {
        $reflection = new ReflectionClass(Siv3Routes::class);

        return [
            [
                $reflection,
                'ENDPOINT_AUTHENTICATE',
            ],
        ];
    }

    /** @dataProvider routes_provider */
    public function test_should_has_constants_with_end_points_siv3(ReflectionClass $reflection, string $route): void
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
