<?php

namespace TradeAppOne\Http\Middleware;

use Authorization\Http\Middleware\ThirdPartiesMiddleware;
use Authorization\Services\ThirdPartiesAccessFactory;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Facades\HeimdallInbound;

class LogAfterRequest
{

    const ALL           = 'all';
    const THIRD_PARTIES = 'thirdparties';

    private $request;
    private $response;

    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;

        $log = Config::get('heimdall.inbound_request');

        switch ($log) {
            case self::ALL:
                $this->log();
                break;
            case self::THIRD_PARTIES:
                $this->thirdParties();
                break;
        }
    }

    public function thirdParties()
    {
        $clientHeader = $this->request->header(ThirdPartiesMiddleware::ACCESS_KEY);
        
        if ($clientHeader) {
            $this->log();
        }
    }

    private function log()
    {
        try {
            HeimdallInbound::index($this->request, $this->response);
        } catch (Exception $exception) {
            Log::alert("Not sending inbound Logs", [$exception]);
        }
    }
}
