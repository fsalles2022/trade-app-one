<?php

namespace TradeAppOne\Http\Controllers;

use Carbon\Carbon;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Logging\Heimdall\HeimdallUserMapper;
use TradeAppOne\Domain\Logging\UserAgent;

class AnalyticsController
{
    public function post()
    {
        $structure = [
            'index' => 'analytics-tao',
            'type'  => '_doc',
            'body'  => [
                'route'    => [
                    'path'    => '',
                    'name'    => '',
                    'element' => '',
                ],
                'session'  => [
                    'userAgent' => new UserAgent(request()->userAgent()),
                    'ip'        => request()->ip()
                ],
                'user'     => HeimdallUserMapper::map(Auth::user()),
                'datetime' => Carbon::now()->toIso8601String()
            ]
        ];
        try {
            $client                             = resolve(ClientBuilder::class);
            $structure['body']['route']['path'] = request()->get('path');
            $structure['body']['route']['path'] = request()->get('name');
            $structure['body']['route']['path'] = request()->get('element');

            $client->index($structure);
            return response()->json(['message' => "Log Saved"], Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            if ($exception instanceof BadRequest400Exception) {
                logger()->alert(
                    'Elasticsearch error in request: ' . $exception->getMessage(),
                    ['message' => 'LOG', 'context' => "LOG"]
                );
            }
            if (! $exception instanceof \RuntimeException) {
                logger()->alert(
                    'ElasticSearch is down: ' . $exception->getMessage(),
                    ['message' => 'LOG', 'context' => "LOG"]
                );
            }
            return response()->json(['message' => "Log Failed"], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
