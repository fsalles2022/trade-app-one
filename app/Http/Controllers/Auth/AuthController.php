<?php

namespace TradeAppOne\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jenssegers\Agent\Agent;
use TradeAppOne\Domain\Services\AuthService;
use TradeAppOne\Http\Controllers\Controller;
use TradeAppOne\Http\Requests\SignInFormRequest;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /** @var AuthService */
    protected $authService;

    /** @var Agent */
    protected $agent;

    public function __construct(AuthService $authService, Agent $agent)
    {
        $this->authService = $authService;
        $this->agent       = $agent;
    }

    public function signin(SignInFormRequest $request)
    {
        $credentials = $request->only('cpf', 'password');

        $alternativeDocument = $this->authService->getAlternativeAuth($credentials['cpf']);
        if ($alternativeDocument) {
            $credentials['cpf'] = $alternativeDocument->cpf;
        }

        $isInvalidCredentials = $this->authService->isCredentialsInvalid($credentials);
        $cpf                  = $credentials['cpf'];

        $restrictionContent = $this->authService->existsRestrictionToSignin($cpf);

        if ($isInvalidCredentials) {
            $this->authService->logFailAttempt($cpf);
            $content = ['error' => trans('messages.token_invalid_credentials')];
            return response()->json($content, Response::HTTP_UNAUTHORIZED);
        }

        if ($restrictionContent) {
            return response()->json($restrictionContent['response'], $restrictionContent['status']);
        }

        if (is_null($restrictionContent)) {
            list($user, $token) = $this->authService->loginUser($cpf);

            $this->authService->logSucessAttempt($cpf);

            $this
                ->authService
                ->logAccess(
                    $request,
                    $user->id
                );

            $response['message'] = trans('messages.token_created');
            $response['data']    = [
                'token' => $token,
                'user' => base64_encode(json_encode($user))
            ];

            return response()
                ->json($response, Response::HTTP_OK)
                ->withHeaders(['Authorization' => "Bearer {$token}"]);
        }

        return response()->json($restrictionContent['response'], $restrictionContent['status']);
    }

    public function getAuthenticatedUserEncrypted()
    {
        $user = $this->authService->getAuthenticatedUser();

        if (is_null($user)) {
            return response(Response::HTTP_UNAUTHORIZED);
        }

        return \response()->json([
            'user' => base64_encode(json_encode($user))
        ]);
    }

    public function getAuthenticatedUser()
    {
        $user = $this->authService->getAuthenticatedUser();

        if (is_null($user)) {
            return response(Response::HTTP_UNAUTHORIZED);
        }

        return $user;
    }

    public function signout(Request $request)
    {
        $user = $this->authService->getAuthenticatedUser();

        if ($user) {
            $this
                ->authService
                ->logAccess(
                    $request,
                    $user->id,
                    false
                );
        }

        JWTAuth::parseToken()->invalidate();

        return response()->json(['message' => trans('messages.signout')]);
    }
}
