<?php

namespace Outsourced\ViaVarejo\Http\Controller;

use Illuminate\Http\Request;
use Outsourced\ViaVarejo\Services\TriangulationViaVarejoService;
use TradeAppOne\Http\Controllers\Controller;

class ViaVarejoCouponController extends Controller
{
    protected $triangulationViaVarejoService;

    public function __construct(TriangulationViaVarejoService $triangulationViaVarejoService)
    {
        $this->triangulationViaVarejoService = $triangulationViaVarejoService;
    }

    public function getCoupon(Request $request)
    {
        $coupon = $this->triangulationViaVarejoService->getCoupon($request->query->all());
        return response()->json(['coupon' => $coupon]);
    }
}
