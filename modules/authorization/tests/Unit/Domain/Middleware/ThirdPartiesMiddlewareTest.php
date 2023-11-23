<?php

namespace Authorization\tests\Unit\Domain\Middleware;

use Authorization\Http\Middleware\ThirdPartiesMiddleware;
use Authorization\Services\ThirdPartiesAccessFactory;
use Illuminate\Http\Request;
use TradeAppOne\Tests\TestCase;

class ThirdPartiesMiddlewareTest extends TestCase
{

    /** @test */
    public function should_not_append_authorization_in_request_when_has_access_key_and_client()
    {
        $request = new Request;
        $request->headers->set(ThirdPartiesMiddleware::ACCESS_KEY, 'INVALID');

        $middleware = new ThirdPartiesMiddleware(resolve(ThirdPartiesAccessFactory::class));

        $middleware->handle($request, function ($requestProcessed) {
            $this->assertNull($requestProcessed->header('authorization'));
        });
    }

    /** @test */
    public function should_not_append_authorization_in_request_when_access_key_is_null()
    {
        $request = new Request;
        $request->headers->set(ThirdPartiesMiddleware::ACCESS_KEY, null);

        $middleware = resolve(ThirdPartiesMiddleware::class);

        $middleware->handle($request, function ($requestProcessed) {
            $this->assertNull($requestProcessed->header('authorization'));
        });
    }
}
