<?php

namespace TradeAppOne\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Services\AuthService;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckMultipleLoginPerUser
{
    /** @var AuthService */
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = md5($request->bearerToken());
        if ($this->checkToken($token)) {
            $this->createLogAccess($request);
            $this->invalidateToken();
            return response()->json(
                ['message' => trans('messages.multiple_access')],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $next($request);
    }

    private function invalidateToken(): bool
    {
        try {
            JWTAuth::parseToken()->invalidate();
            return true;
        } catch (\Throwable $ex) {
            return false;
        }
    }

    private function checkToken($token): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return $user->activeToken != $token;
    }

    private function createLogAccess(Request $request): void
    {
        $user = Auth::user();
        if ($user) {
            $this
                ->authService
                ->logAccess(
                    $request,
                    $user->id,
                    false
                );
        }
    }
}
