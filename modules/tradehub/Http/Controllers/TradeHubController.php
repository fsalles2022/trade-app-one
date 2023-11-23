<?php

declare(strict_types=1);

namespace Tradehub\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
use TradeAppOne\Http\Controllers\Controller;
use Tradehub\Services\TradeHubService;
use Tradehub\Services\UpdateSaleService;

class TradeHubController extends Controller
{

    /**
     * @var TradeHubService
     */
    private $tradeHubService;

    /** @var UpdateSaleService */
    private $updateSaleService;

    public function __construct(TradeHubService $tradeHubService, UpdateSaleService $updateSaleService)
    {
        $this->tradeHubService = $tradeHubService;
        $this->updateSaleService = $updateSaleService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sendPortabilityToken(Request $request): JsonResponse
    {
        return response()->json(
            $this->tradeHubService->sendVerificationToken($request->all())
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sendPortabilityTokenTim(Request $request): JsonResponse
    {
        return response()->json(
            $this->tradeHubService->sendVerificationTokenTim($request->all())
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function checkPortabilityToken(Request $request): JsonResponse
    {
        return response()->json(
            $this->tradeHubService->checkTheSentToken($request->all())
        );
    }

    public function receiveSaleUpdateByTradeHub(Request $request): JsonResponse
    {
        return response()->json(
            $this->updateSaleService->update($request->all())
        );
    }
}
