<?php

namespace Buyback\Http\Controllers;

use Buyback\Assistance\TradeInSaleAssistance;
use Buyback\Components\Vouchers\Factories\TradeInVoucherFactory;
use Buyback\Http\Requests\RevaluationFormRequest;
use Buyback\Http\Requests\VoucherFormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\PermissionActions;
use TradeAppOne\Http\Controllers\Controller;

class BuybackSaleAssistanceController extends Controller
{
    private $tradeInSaleAssistance;

    public function __construct(TradeInSaleAssistance $tradeInSaleAssistance)
    {
        $this->tradeInSaleAssistance = $tradeInSaleAssistance;
    }

    public function revaluation(RevaluationFormRequest $request): JsonResponse
    {
        return response()->json([
            'message' => trans('buyback::messages.evaluation_success'),
            'data' => $this->tradeInSaleAssistance->revaluation($request->validated()),
        ], Response::HTTP_CREATED);
    }

    public function voucher(VoucherFormRequest $request): string
    {
        $service = $this->tradeInSaleAssistance->canGenerateVoucher($request->serviceTransaction);
        return TradeInVoucherFactory::run($service);
    }

    public function burnVoucher(VoucherFormRequest $request): JsonResponse
    {
        return response()->json([
            'message' => trans('buyback::messages.voucher_burned'),
            'data'    => $this->tradeInSaleAssistance->burnVoucher($request->get('serviceTransaction'))
        ], Response::HTTP_OK);
    }
}
