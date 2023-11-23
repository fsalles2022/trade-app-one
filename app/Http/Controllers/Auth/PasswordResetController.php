<?php

namespace TradeAppOne\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Services\AuthService;
use TradeAppOne\Domain\Services\PasswordResetService;
use TradeAppOne\Domain\Services\UserService;
use TradeAppOne\Exceptions\BusinessExceptions\UserNotFoundException;
use TradeAppOne\Http\Controllers\Controller;
use TradeAppOne\Http\Requests\PasswordResetRequest;
use TradeAppOne\Http\Requests\UserResetPasswordRequest;

class PasswordResetController extends Controller
{

    protected $passwordResetService;
    protected $userService;
    protected $authService;

    public function __construct(PasswordResetService $passwordResetService, UserService $userService, AuthService $authService)
    {
        $this->passwordResetService = $passwordResetService;
        $this->userService          = $userService;
        $this->authService          = $authService;
    }

    public function index(Request $request)
    {
        return $this->passwordResetService->filter($request->all());
    }

    public function postRequestPasswordReset(PasswordResetRequest $request): JsonResponse
    {
        $credentials = $request->only('cpf');

        $alternativeDocument = $this->authService->getAlternativeAuth($credentials['cpf']);
        if ($alternativeDocument) {
            $request['cpf'] = $alternativeDocument->cpf;
        }

        if (isset($request->password)) {
            $passwordsMatch = $this->passwordResetService->VerifyManagerPassword($request->cpf, $request->password);
            if ($passwordsMatch) {
                $content['message'] = trans('messages.password_updated');
                return response()->json($content, Response::HTTP_OK);
            }
            $content['message'] = trans('messages.failed');
            return response()->json($content, Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $requestAccepted = $this->passwordResetService->sendRequestToManager($request->cpf);
            if ($requestAccepted) {
                $content['message'] = trans('messages.request_recovery_sent');
                return response()->json($content, Response::HTTP_OK);
            }
            $content['message'] = trans('messages.cant_recovery_password');
            return response()->json($content, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function putResponseRequestPasswordReset(PasswordResetRequest $request): JsonResponse
    {
        $userLogged      = Auth::user();
        $managerResponse = $this->passwordResetService->managerResponse($userLogged->id, $request->id, $request->response);
        if ($managerResponse) {
            $content['message'] = trans('messages.request_approved');
            return response()->json($content, Response::HTTP_OK);
        }
        $content['message'] = trans('messages.request_rejected');
        return response()->json($content, Response::HTTP_OK);
    }

    public function generateVerificationTokenForReset(UserResetPasswordRequest $request): JsonResponse
    {
        $request->validated();
        if ($this->userService->verifyTokenGenerated($request->user())) {
            return response()->json(
                ['message' => trans('messages.request_recovery_duplicated')],
                Response::HTTP_CONFLICT
            );
        }
        if (! $this->passwordResetService->verifyUserPassword($request->user()->cpf, $request->get('password'))) {
            return response()->json(
                [
                'message' => trans('messages.failed'),
                'invalidCredential' => true],
                Response::HTTP_CONFLICT
            );
        }
        $verificationCode = $this->userService->generateAndRegisterVerificationCode($request->user());
        if ($verificationCode) {
            $content = [
                'message' => trans('messages.token_created'),
                'verificationToken' => $verificationCode
            ];
            return response()->json($content, Response::HTTP_OK);
        }
        $content['message'] = trans('messages.request_recovery_error');
        return response()->json($content, Response::HTTP_CONFLICT);
    }

    public function resetPasswordWithVerificationToken(UserResetPasswordRequest $request): JsonResponse
    {
        $request->validated();
        $user = $this->userService->userByVerificationCode($request->get('verificationToken'));
        throw_if($user === null, UserNotFoundException::class);
        $result = $this->userService->updateUserPassword($request->get('password'), $request->get('verificationToken'));
        if ($result) {
            return response()->json(['message' => trans('messages.password_updated')], Response::HTTP_OK);
        }
        return response()->json(['message' => trans('messages.password_updated_error')], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
