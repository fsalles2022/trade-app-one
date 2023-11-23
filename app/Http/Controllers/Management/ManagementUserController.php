<?php

namespace TradeAppOne\Http\Controllers\Management;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\UserStatus;
use TradeAppOne\Domain\Services\AuthService;
use TradeAppOne\Domain\Services\UserService;
use TradeAppOne\Http\Controllers\Controller;

class ManagementUserController extends Controller
{
    protected $authService;
    protected $userService;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function postPersonify(Request $request): JsonResponse
    {
        list($user, $token) = $this->authService->loginUser($request->get('cpf'));

        $this->response['data']['token'] = $token;
        $this->response['data']['user']  = $user;

        return response()
            ->json($this->response, Response::HTTP_OK)
            ->withHeaders(['Authorization' => "Bearer {$token}"]);
    }

    public function postDisable(Request $request): JsonResponse
    {
        $disabled = $this->userService->changeUserStatus($request->get('cpf'), UserStatus::INACTIVE);
        if ($disabled) {
            return response()->json(['message' => 'Desativado com sucesso!']);
        }
        return response()->json($this->response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function postEnable(Request $request): JsonResponse
    {
        $enabled = $this->userService->changeUserStatus($request->get('cpf'), UserStatus::ACTIVE);
        if ($enabled) {
            return response()->json(['message' => 'Ativado com Sucesso!']);
        }
        return response()->json($this->response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
