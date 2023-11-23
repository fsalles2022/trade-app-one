<?php

namespace TradeAppOne\Exceptions;

use ClaroBR\Exceptions\NoAccessToSivException;
use ClaroBR\Exceptions\SivInvalidCredentialsException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use TradeAppOne\Exceptions\BusinessExceptions\BusinessRuleExceptions;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        TokenInvalidException::class,
        BusinessRuleExceptions::class,
        NoAccessToSivException::class,
        SivInvalidCredentialsException::class
    ];

    protected $dontFlash = ['password', 'password_confirmation',];

    public function report(Exception $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }
        parent::report($exception);
    }

    public function render($request, Exception $exception)
    {
        if ($exception instanceof AuthorizationException) {
            return response()->json(['message' => trans('messages.not_authorized')], 403);
        }

        if ($exception instanceof UnauthorizedHttpException ||
            $exception instanceof TokenBlacklistedException ||
            $exception instanceof TokenInvalidException) {
            return response()->json(['message' => trans('messages.session_expired')], 401);
        }

        if ($exception instanceof \ReflectionException) {
            return response(['errors' => [$exception->getMessage()]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if ($exception instanceof ValidationException) {
            return response(['errors' => $exception->validator->errors()->getMessages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if ($exception instanceof ConnectionTimeoutException) {
            throw new NoDatabase($exception->getMessage());
        }
        if ($exception->getCode() == Response::HTTP_INTERNAL_SERVER_ERROR) {
            return response(['errors' => [$exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return parent::render($request, $exception);
    }
}
