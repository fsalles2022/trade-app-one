<?php

declare(strict_types=1);

namespace ClaroBR\Http\Controllers;

use ClaroBR\Http\Requests\Siv3Request;
use ClaroBR\Services\Siv3Service;
use Illuminate\Http\JsonResponse;
use TradeAppOne\Http\Controllers\Controller;

class Siv3Controller extends Controller
{
    /** @var Siv3Service */
    private $siv3Service;

    public function __construct(Siv3Service $siv3Service)
    {
        $this->siv3Service = $siv3Service;
    }

    public function sendAuthorization(Siv3Request $request): JsonResponse
    {
        return response()->json(
            $this->siv3Service->sendDataTocheckAuthorization($request->validated())
        );
    }

    public function checkAuthorization(Siv3Request $request): JsonResponse
    {
        return response()->json(
            $this->siv3Service->checkAuthorizationCode($request->validated())
        );
    }
}
