<?php

namespace TradeAppOne\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \TradeAppOne\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \TradeAppOne\Http\Middleware\TrustProxies::class,
        \Barryvdh\Cors\HandleCors::class,
        \TradeAppOne\Http\Middleware\I18nRequestHandler::class,
        \TradeAppOne\Domain\Logging\Heimdall\RequestId::class,
        \TradeAppOne\Http\Middleware\LogAfterRequest::class,
        \Authorization\Http\Middleware\ThirdPartiesMiddleware::class,
    ];

    protected $middlewareGroups = [
        'api'    => [
            'throttle:10000,1',
            'bindings',
            'jwt',
            'sentry',
            'denyMultiAccess',
        ],
        'signin' => [
            'throttle:1000,1',
            'bindings',
            'sentry',
        ]
    ];

    protected $routeMiddleware = [
        'auth'       => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'   => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can'        => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'      => \TradeAppOne\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle'   => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'jwt'        => \TradeAppOne\Http\Middleware\RefreshToken::class,
        'sentry'     => \TradeAppOne\Http\Middleware\SentryLogMiddleware::class,
        'denyMultiAccess' => \TradeAppOne\Http\Middleware\CheckMultipleLoginPerUser::class,
    ];
}
