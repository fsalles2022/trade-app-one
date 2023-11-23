<?php

namespace Buyback\Http\Controllers;

use Buyback\Http\Requests\OfferDeclinedFormRequest;
use Buyback\Http\Requests\TradeInOfferDeclinedFormRequest;
use Buyback\Services\OfferDeclinedService;
use Buyback\Services\TradeInService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Http\Controllers\Controller;

class TradeInOfferDeclinedController extends Controller
{
    private $offerDeclinedService;
    private $tradeInService;

    public function __construct(OfferDeclinedService $offerDeclinedService, TradeInService $tradeInService)
    {
        $this->offerDeclinedService = $offerDeclinedService;
        $this->tradeInService       = $tradeInService;
    }

    public function index(TradeInOfferDeclinedFormRequest $request)
    {
        $user = $request->user();
        return $this->offerDeclinedService->paginateDeclinedOffersByUser($user, $request->validated());
    }

    public function export()
    {
        $user = Auth::user();
        return $this->offerDeclinedService->exportDeclinedOffersByUser($user)->export();
    }

    public function offerDeclinedByCustomer(OfferDeclinedFormRequest $offerDeclinedFormRequest)
    {
        $user          = Auth::user();
        $offerDeclined = $this->tradeInService->registerOfferDeclined($user, $offerDeclinedFormRequest->validated());
        return response()->json($offerDeclined, Response::HTTP_OK);
    }
}
