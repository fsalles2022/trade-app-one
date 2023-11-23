<?php

use Carbon\Carbon;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Logging\UserAgent;

Route::post('/log', function () {
    try {
        $client = resolve(ClientBuilder::class);
        $params = [
            'index' => 'heimdall-modal-m4u',
            'type' => '_doc',
            'body' => [
                'userAgent' => new UserAgent(request()->userAgent()),
                'ip' => request()->ip(),
                'user' => Auth::user(),
                'datetime' => Carbon::now()->toIso8601String()
            ]
        ];

        $params['body'] += request()->toArray();

        $client->index($params);
        return response()->json(['message' => "Log Saved"], Response::HTTP_CREATED);
    } catch (Exception $exception) {
        if ($exception instanceof BadRequest400Exception) {
            logger()->alert(
                'Elasticsearch error in request: ' . $exception->getMessage(),
                ['message' => 'LOG', 'context' => "LOG"]
            );
        }
        if (!$exception instanceof \RuntimeException) {
            logger()->alert(
                'ElasticSearch is down: ' . $exception->getMessage(),
                ['message' => 'LOG', 'context' => "LOG"]
            );
        }
        return response()->json(['message' => "Log Failed"], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
});
