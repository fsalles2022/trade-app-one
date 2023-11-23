<?php

namespace Buyback\Http\Controllers;

use Buyback\Http\Requests\DevicesFormRequest;
use Buyback\Http\Requests\PriceFormRequest;
use Buyback\Http\Requests\QuizFormRequest;
use Buyback\Services\TradeInService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use TradeAppOne\Http\Controllers\Controller;

class BuybackController extends Controller
{
    private $tradeInService;

    public function __construct(TradeInService $tradeInService)
    {
        $this->tradeInService = $tradeInService;
    }

    public function indexDeclinedOffers()
    {
        $user = auth::user();
        return $this->buybackService->getDeclinedOffers($user);
    }

    public function devices(DevicesFormRequest $devicesFormRequest)
    {
        $networkId = Auth::user()->pointsOfSale->first()->networkId;

        $filters = $devicesFormRequest->validated();
        // FILTRO REFERENTE A LISTAR DISPOSITIVOS DA REDE QUE SEJAM SOMENTE PARA OP. DE TRADEIN
        data_set($filters, 'devices_network.isPreSale', 0);

        $devicesList = $this->tradeInService->getDevicesWithFilters([$networkId], $filters);
        if ($devicesList->isEmpty()) {
            return response()->json($devicesList, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json($devicesList, Response::HTTP_OK);
    }

    public function questions(QuizFormRequest $quizFormRequest)
    {
        $deviceId  = $quizFormRequest->deviceId;
        $networkId = Auth::user()->pointsOfSale()->first()->networkId;
        $questions = $this->tradeInService->getQuestions($deviceId, $networkId);

        return response()->json(['questions' => $questions, 'deviceId' => $deviceId], Response::HTTP_OK);
    }

    public function price(PriceFormRequest $priceFormRequest)
    {
        $deviceId        = $priceFormRequest->deviceId;
        $questionsAnswer = $priceFormRequest->questions;
        $networkId       = Auth::user()->getNetwork()->id;

        $rating = $this->tradeInService->getPrice($deviceId, $networkId, $questionsAnswer);
        $bonus  = $this->tradeInService->getBonusPrice($deviceId, $networkId, $rating);

        $integratedResponse = $rating->toArray();
        data_set($integratedResponse, 'bonus', $bonus->toArray());

        return response()->json($integratedResponse, Response::HTTP_OK);
    }

    public function findWatch(DevicesFormRequest $devicesFormRequest)
    {
        return $this->tradeInService->findWatch($devicesFormRequest->validated());
    }

    /**
    * @param DevicesFormRequest $devicesFormRequest
    */
    public function findIpad(DevicesFormRequest $devicesFormRequest): Collection
    {
        return $this->tradeInService->findIpad($devicesFormRequest->validated());
    }
}
