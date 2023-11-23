<?php

declare(strict_types=1);

namespace Tradehub\Tests\Unit\Connection;

use ReflectionClass;
use TradeAppOne\Tests\TestCase;
use Tradehub\Connection\TradeHubRoutes;

class TradeHubRoutesTest extends TestCase
{
    /** @return array[] */
    public function routes_provider(): array
    {
        $reflection = new ReflectionClass(TradeHubRoutes::class);

        return [
            [
                $reflection,
                [
                    'ENDPOINT_AUTHENTICATE',
                    'ENDPOINT_AUTHENTICATE_SELLER',
                    'ENDPOINT_SEND_TOKEN_PORTABILITY',
                    'ENDPOINT_VALIDATE_TOKEN_PORTABILITY'
                ],
            ],
        ];
    }

    /** @dataProvider routes_provider */
    public function test_should_has_constants_with_end_points_tradehub(ReflectionClass $reflection, array $routes): void
    {
        array_map(function ($route) use ($reflection) {
            $this->assertTrue(
                $this->is_route_valid($reflection, $route),
                "Route {$route} not defined or empty"
            );
        }, $routes);
    }

    private function is_route_valid(ReflectionClass $reflection, string $route): bool
    {
        return $reflection->hasConstant($route) && empty($reflection->getConstant($route)) === false;
    }
}
