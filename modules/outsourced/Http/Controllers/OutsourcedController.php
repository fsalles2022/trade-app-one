<?php

namespace Outsourced\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Outsourced\Assistance\OutsourcedFactory;
use Outsourced\Enums\Crafts;
use TradeAppOne\Http\Controllers\Controller;

class OutsourcedController extends Controller
{
    public function getDevice(string $deviceIdentifier, Request $request)
    {
        $network  = $request->user()->getNetwork()->slug;
        $instance = OutsourcedFactory::make($network, Crafts::DEVICES);

        $device = $instance->findDevice($deviceIdentifier)->toArray();
        return response()->json($device, Response::HTTP_OK);
    }
}
