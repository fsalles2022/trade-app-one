<?php

namespace TradeAppOne\Http\Middleware;

use Closure;

class SentryLogMiddleware
{
    public function handle($request, Closure $next)
    {
        if (app()->bound('sentry')) {
            $sentry = app('sentry');

            if (auth()->check()) {
                $sentry->user_context(auth()->user()->toArray());
            } else {
                $sentry->user_context(['id' => null]);
            }
        }

        return $next($request);
    }
}
