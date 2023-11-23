<?php

namespace TradeAppOne\Http\Middleware;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class RefreshToken extends BaseMiddleware
{
    public function handle($request, \Closure $next)
    {
        $this->checkForToken($request);
        try {
            if (! $this->auth->parseToken()->authenticate()) {
                throw new UnauthorizedHttpException('jwt-auth', 'User not found');
            }
            return $next($request);
        } catch (TokenExpiredException $t) {
            $payload      = $this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray();
            $key          = 'block_refresh_token_for_user_' . $payload['sub'];
            $cachedBefore = (int) Cache::has($key);
            if ($cachedBefore) {
                Auth::onceUsingId($payload['sub']);
                return $next($request);
            }
            try {
                $newtoken    = $this->auth->refresh();
                $gracePeriod = $this->auth->manager()->getBlacklist()->getGracePeriod();
                $expiresAt   = Carbon::now()->addSeconds($gracePeriod);
                Cache::put($key, $newtoken, $expiresAt);
            } catch (JWTException $e) {
                throw new UnauthorizedHttpException('jwt-auth', $e->getMessage(), $e, $e->getCode());
            }
        }
        $user = JWTAuth::setToken($newtoken)->toUser();
        Auth::login($user, false);
        $response = $next($request);

        return $this->setAuthenticationHeader($response, $newtoken);
    }
}
