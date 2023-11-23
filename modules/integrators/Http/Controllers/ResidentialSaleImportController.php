<?php

namespace Integrators\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Integrators\Http\Requests\ResidentialSaleForm;
use Integrators\Services\ResidentialSaleService;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHooksFactory;
use TradeAppOne\Http\Controllers\Controller;

class ResidentialSaleImportController extends Controller
{
    protected $residentialService;

    public function __construct(ResidentialSaleService $residentialSaleService)
    {
        $this->residentialService = $residentialSaleService;
    }

    public function store(ResidentialSaleForm $formData): JsonResponse
    {
        if ($formData->validated()) {
            $sale = $this->residentialService->handle($formData->all());
            if ($sale instanceof Sale) {
                $response = [
                    'body' => trans('messages.sale_saved'),
                    'statusCode' => Response::HTTP_CREATED,
                    'type' => 'success'
                ];
                foreach ($sale->services as $service) {
                    NetworkHooksFactory::run($service);
                }
            } else {
                $response = [
                    'body' => trans('messages.sale_duplicated'),
                    'statusCode' => Response::HTTP_CONFLICT,
                    'type' => 'error'
                ];
            }

            return response()->json($response, $response['statusCode']);
        }
        return response()->json(['messages' => trans('messages.sale_not_saved')], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function update(ResidentialSaleForm $formData): JsonResponse
    {
        if ($formData->validated()) {
            $sale = $this->residentialService->update($formData->all());
            if ($sale instanceof Sale) {
                $response = [
                    'body' => trans('messages.sale_updated'),
                    'statusCode' => Response::HTTP_OK,
                    'type' => 'success'
                ];
                foreach ($sale->services as $service) {
                    NetworkHooksFactory::run($service);
                }
            } else {
                $response = [
                    'body' => trans('messages.sale_not_saved'),
                    'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'type' => 'error'
                ];
            }

            return response()->json($response, $response['statusCode']);
        }
    }
}
