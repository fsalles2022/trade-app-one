<?php

namespace TradeAppOne\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Http\Requests\BackOfficeFormRequest;

class BackOfficeController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function store(BackOfficeFormRequest $request): JsonResponse
    {
        hasPermissionOrAbort(SalePermission::getFullName(SalePermission::CREATE_BACKOFFICE));

        $user               = $request->user();
        $data               = $request->validated();
        $serviceTransaction = $data['serviceTransaction'];
        unset($data['serviceTransaction']);

        $this->saleService->saveBackOffice($serviceTransaction, $user, $data);

        $message = trans('messages.backoffice.success_save_comment');
        return response()->json(['message' => $message], Response::HTTP_OK);
    }
}
