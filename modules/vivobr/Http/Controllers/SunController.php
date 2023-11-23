<?php

namespace VivoBR\Http\Controllers;

use ClaroBR\Exceptions\PlansNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\Sale\ServiceOptionsFilter;
use TradeAppOne\Http\Controllers\Controller;
use TradeAppOne\Http\Requests\ActivationFormRequest;
use VivoBR\Http\FormRequests\VivoBrProductsFormRequest;
use VivoBR\Services\VivoBrMapPlansService;
use VivoBR\Services\VivoBrSaleAssistance;
use VivoBR\Services\VivoBRService;
use VivoTradeUp\Repositories\VivoM4uControleCartao;

class SunController extends Controller
{
    protected $assistance;
    protected $service;

    public function __construct(VivoBrSaleAssistance $assistance, VivoBRService $service)
    {
        $this->assistance = $assistance;
        $this->service    = $service;
    }

    public function products(VivoBrProductsFormRequest $request): JsonResponse
    {
        $options     = $request->validated();
        $requestUser = $request->user();
        $networkSlug = $requestUser->getNetwork()->slug;

        $plansMapped = collect([]);

        $serviceOptions = ServiceOptionsFilter::make($requestUser, [
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::VIVO,
            'operation' => Operations::VIVO_CONTROLE_CARTAO
        ])->verifyM4uTradeUp()->filter();

        if (in_array(ServiceOptionsFilter::VIVO_CONTROLE_CARTAO_M4U, $serviceOptions, true)) {
            $areaCode    = data_get($options, 'areaCode', null);
            $plansMapped = VivoBrMapPlansService::map([
                'planos' => VivoM4uControleCartao::getControleCartaoM4u($networkSlug, $areaCode)
            ]);
        }

        if (! in_array(ServiceOptionsFilter::VIVO_CONTROLE_CARTAO_M4U, $serviceOptions, true)) {
            $plansMapped = $this->assistance->getProductsByFilters($networkSlug, $options);
        }

        if ($plansMapped->isEmpty()) {
            throw new PlansNotFoundException();
        }

        return response()->json($plansMapped->toArray());
    }

    public function postUser(Request $request)
    {
        return $this->assistance->createUser($request->query->all());
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

    public function domains(Request $request)
    {
        $queryString = $request->query();
        $operation   = data_get($queryString, 'operation', null);
        return response()->json($this->service->domains($operation));
    }

    public function totalization($cpf): JsonResponse
    {
        $response = $this->assistance->getCustomerTotalization($cpf);
        $status   = json_encode($response->get('dados.cliente.status'));
        $name     = ucwords(strtolower($response->get('dados.cliente.nome')));

        return response()->json([
            'message' => trans("sun::messages.apiSun.totalization.{$status}", ['name' => $name]),
            'data' => $response->get('dados')
        ]);
    }
}
