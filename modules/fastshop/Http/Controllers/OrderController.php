<?php

namespace FastShop\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        return response()->json(['status' => true], Response::HTTP_OK);
    }
}
