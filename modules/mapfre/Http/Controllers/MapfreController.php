<?php

namespace Mapfre\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Http\Controllers\Controller;
use TradeAppOne\Http\Requests\ActivationFormRequest;

class MapfreController extends Controller
{
    public function integrateService(ActivationFormRequest $request)
    {
        return Response::HTTP_OK;
    }
}
