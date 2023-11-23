<?php

namespace TimBR\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use TimBR\Enumerators\TimBRCacheables;

class TimAuthController
{
    protected $service;

    public function networkAuthentication(Request $request, $network)
    {
        Log::info('TIM-COOKIE', [
            'network'    => $network,
            'cookies'    => $request->cookies->all(),
            'parameters' => $request->all(),
            'origin'     => $request->getBaseUrl(),
        ]);
    }
}
