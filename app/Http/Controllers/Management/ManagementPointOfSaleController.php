<?php

namespace TradeAppOne\Http\Controllers\Management;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use TradeAppOne\Domain\Services\PointOfSaleReaderService;
use TradeAppOne\Http\Controllers\Controller;
use TradeAppOne\Policies\PointOfSalePolicy;

class ManagementPointOfSaleController extends Controller
{
    protected $service;
    protected $pointOfSalePolicy;

    public function __construct(PointOfSaleReaderService $service, PointOfSalePolicy $pointOfSalePolicy)
    {
        $this->service           = $service;
        $this->pointOfSalePolicy = $pointOfSalePolicy;
    }

    public function export(Request $request): StreamedResponse
    {
        $this->pointOfSalePolicy->export(Auth::user());

        return $this->service->export(Auth::user(), $request->all());
    }
}
