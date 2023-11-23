<?php

namespace Uol\Http\Controllers;

use Illuminate\Http\Response;
use TradeAppOne\Http\Controllers\Controller;
use Uol\Services\UolService;

class UolController extends Controller
{
    private $uolService;

    public function __construct(UolService $uolService)
    {
        $this->uolService = $uolService;
    }

    public function plans()
    {
        $plans =  $this->uolService->getPlans();
        return response()->json($plans, Response::HTTP_OK);
    }
}
