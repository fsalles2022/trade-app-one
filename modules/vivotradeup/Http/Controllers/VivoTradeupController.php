<?php

namespace VivoTradeUp\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use TradeAppOne\Http\Controllers\Controller;
use TradeAppOne\Http\Requests\ActivationFormRequest;
use VivoTradeUp\Assistances\VivoTradeUpAssistance;

class VivoTradeupController extends Controller
{
    protected $assistance;

    public function __construct(VivoTradeUpAssistance $assistance)
    {
        $this->assistance = $assistance;
    }

    public function confirmControleCartao(ActivationFormRequest $request): JsonResponse
    {
        if ($this->assistance->confirmControleCartao($request->all())) {
            $this->response['message'] = trans('messages.default_success');
            return response()->json($this->response, Response::HTTP_ACCEPTED);
        }
        $this->response['message'] = trans('messages.default');
        return response()->json($this->response, Response::HTTP_PRECONDITION_FAILED);
    }
}
