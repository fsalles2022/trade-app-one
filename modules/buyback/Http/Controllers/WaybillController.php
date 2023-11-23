<?php

namespace Buyback\Http\Controllers;

use Buyback\Enumerators\WaybillPermissions;
use Buyback\Http\Requests\WaybillFormRequest;
use Buyback\Services\WaybillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use TradeAppOne\Exceptions\BusinessExceptions\SaleNotFoundException;
use TradeAppOne\Http\Controllers\Controller;

class WaybillController extends Controller
{
    /**
     * @var WaybillService
     */
    private $waybillService;

    public function __construct(WaybillService $waybillService)
    {
        $this->waybillService = $waybillService;
    }

    public function generate(WaybillFormRequest $request)
    {
        hasPermissionOrAbort(WaybillPermissions::getFullName(WaybillPermissions::CREATE));

        $user      = $request->user();
        $cnpj      = $request->get('cnpj');
        $operation = $request->get('operation');

        return $this->waybillService->generateWaybill($user, $cnpj, $operation);
    }

    public function getAvailable(WaybillFormRequest $request): JsonResponse
    {
        $user     = $request->user();
        $filters  = $request->validated();
        $waybills =  $this->waybillService->getWaybillsAvailable($user, $filters);

        return response()->json($waybills, Response::HTTP_OK);
    }

    /**
     * @throws SaleNotFoundException
     */
    public function checkDevice(WaybillFormRequest $request): JsonResponse
    {
        $user           = $request->user();
        $serviceChecked = $this->waybillService->checkWithdrawnDevice($user, $request->validated());
        return response()->json(['service' => $serviceChecked], Response::HTTP_OK);
    }
}
