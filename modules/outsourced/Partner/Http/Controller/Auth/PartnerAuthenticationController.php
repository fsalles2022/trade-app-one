<?php

namespace Outsourced\Partner\Http\Controller\Auth;

use Illuminate\Http\JsonResponse;
use Outsourced\Partner\Http\Requests\AuthPartnerFormRequest;
use Outsourced\Partner\Services\AuthPartnerService;
use TradeAppOne\Http\Controllers\Controller;

class PartnerAuthenticationController extends Controller
{
    protected $authPartnerService;

    public function __construct(AuthPartnerService $authPartnerService)
    {
        $this->authPartnerService = $authPartnerService;
    }

    public function grantAccess(AuthPartnerFormRequest $request): JsonResponse
    {
        $response = $this->authPartnerService->getAuthenticatedUrl($request->validated());
        return response()->json(['url' => $response]);
    }

    public function getCredentialByToken(string $md5): JsonResponse
    {
        $response = $this->authPartnerService->getCredentialsFromMD5($md5);
        return response()->json($response);
    }
}
