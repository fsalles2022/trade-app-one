<?php

namespace VivoBR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Services\UserThirdPartyRegistrations\UsersRegistrationCommandService;
use TradeAppOne\Http\Controllers\Controller;

class ManagementController extends Controller
{
    protected $service;

    public function __construct(UsersRegistrationCommandService $service)
    {
        $this->service = $service;
    }

    public function syncUser(Request $request)
    {
        $result = $this->service->process(['user' => $request->cpf]);
        if ($result->isNotEmpty()) {
            return response()->json(['message' => 'Concluído com sucesso']);
        }
        return response()->json(
            ['message' => 'Houve uma falha na sincronia, tente novamente'],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
