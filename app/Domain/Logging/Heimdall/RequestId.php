<?php

namespace TradeAppOne\Domain\Logging\Heimdall;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class RequestId
{
    const KEY = 'requestId';

    /**
     * @var Response
     */
    private $response;

    public function handle($request, Closure $next)
    {
        $uniqId = uniqid();
        $request->headers->set(RequestId::KEY, $uniqId);

        $this->response = $next($request);
        $this->response->headers->set(RequestId::KEY, $uniqId);

        return $this->response;
    }
}
