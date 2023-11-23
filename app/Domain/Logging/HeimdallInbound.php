<?php

namespace TradeAppOne\Domain\Logging;

use Authorization\Http\Middleware\ThirdPartiesMiddleware;
use Carbon\Carbon;
use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use TradeAppOne\Domain\Logging\Heimdall\HeimdallUserMapper;

class HeimdallInbound
{
    private $client;

    const INDEX = 'heimdall_inbound';

    public function __construct()
    {
        $host = config('heimdall.host');
        $port = config('heimdall.port');

        $this->client = ClientBuilder::create()
            ->setHosts([$host . ':' . $port])
            ->build();
    }

    public function index(Request $request, Response $response)
    {
        $content      = $response->getContent();
        $canBePrinted = ctype_print($content);
        $clientHeader = $request->header(ThirdPartiesMiddleware::ACCESS_KEY);

        $body = [
            'ip' => $request->getClientIp(),
            'method' => $request->getMethod(),
            'status' => $response->getStatusCode(),
            'contentType' => $request->getContentType(),
            'requestUri' => $request->getRequestUri(),
            'client' => $clientHeader,
            'path' => $request->path(),
            'url' => $request->fullUrl(),
            'requestId' => $request->header('requestId'),
            'request' => $request->getContent(),
            'response' => $canBePrinted ? $content : "NOT_PRINTABLE",
            'user' => HeimdallUserMapper::map($request->user()),
            'datetime' => Carbon::now()->toIso8601String()
        ];

        return $this->client->index([
            'type' => 'doc',
            'index' => self::INDEX,
            'body' => $body
        ]);
    }
}
