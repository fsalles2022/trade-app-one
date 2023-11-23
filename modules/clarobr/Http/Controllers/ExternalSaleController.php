<?php
declare(strict_types=1);

namespace ClaroBR\Http\Controllers;

use ClaroBR\Http\Requests\CheckExternalSaleFormRequest;
use ClaroBR\Http\Requests\ExternalSaleFormRequest;
use ClaroBR\Services\ExternalSaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use TradeAppOne\Http\Controllers\Controller;

class ExternalSaleController extends Controller
{
    /** @var ExternalSaleService */
    private $externalSaleService;

    public function __construct(ExternalSaleService $externalSaleService)
    {
        $this->externalSaleService = $externalSaleService;
    }

    public function checkExternalSale(CheckExternalSaleFormRequest $request): JsonResponse
    {
        $saleChecked = $this->externalSaleService->checkExternalSaleExist($request->validated());

        return response()->json(
            data_get($saleChecked, 'response', []),
            data_get($saleChecked, 'statusCode', Response::HTTP_OK)
        );
    }

    public function createExternalSale(ExternalSaleFormRequest $externalSalePrePago): JsonResponse
    {
        $response = $this->externalSaleService->insertExternalSale($externalSalePrePago->validated());

        return response()->json(
            data_get($response, 'response'),
            Response::HTTP_CREATED
        );
    }
}
